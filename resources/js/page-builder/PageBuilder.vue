<template>
  <div class="page-builder" :class="{ 'preview-mode': previewMode }">
    <!-- Top Toolbar -->
    <div class="pb-toolbar">
      <div class="toolbar-left">
        <button @click="toggleSidebar" class="btn-icon">
          <i class="fas fa-bars"></i>
        </button>
        <div class="page-title">
          <input v-model="pageTitle" @blur="updatePageTitle" placeholder="Page Title" />
        </div>
      </div>
      
      <div class="toolbar-center">
        <div class="device-switcher">
          <button 
            v-for="device in devices" 
            :key="device.name"
            @click="currentDevice = device.name"
            :class="{ active: currentDevice === device.name }"
            class="device-btn"
          >
            <i :class="device.icon"></i>
          </button>
        </div>
      </div>
      
      <div class="toolbar-right">
        <button @click="showRevisions = true" class="btn-secondary">
          <i class="fas fa-history"></i> Revisions
        </button>
        <button @click="togglePreview" class="btn-secondary">
          <i class="fas fa-eye"></i> Preview
        </button>
        <button @click="savePage" class="btn-primary" :disabled="saving">
          <i class="fas fa-save"></i> {{ saving ? 'Saving...' : 'Save' }}
        </button>
        <button @click="publishPage" class="btn-success" v-if="pageStatus !== 'published'">
          <i class="fas fa-globe"></i> Publish
        </button>
      </div>
    </div>

    <div class="pb-main">
      <!-- Left Sidebar - Elements Panel -->
      <div class="pb-sidebar" :class="{ collapsed: sidebarCollapsed }">
        <div class="sidebar-tabs">
          <button 
            v-for="tab in sidebarTabs" 
            :key="tab.id"
            @click="activeSidebarTab = tab.id"
            :class="{ active: activeSidebarTab === tab.id }"
            class="tab-btn"
          >
            <i :class="tab.icon"></i>
            <span>{{ tab.name }}</span>
          </button>
        </div>

        <div class="sidebar-content">
          <!-- Elements Tab -->
          <div v-if="activeSidebarTab === 'elements'" class="elements-panel">
            <div class="search-box">
              <input v-model="elementSearch" placeholder="Search elements..." />
            </div>
            
            <div v-for="(elements, category) in filteredElements" :key="category" class="element-category">
              <h4>{{ category }}</h4>
              <div class="element-grid">
                <div 
                  v-for="element in elements" 
                  :key="element.id"
                  draggable="true"
                  @dragstart="startDragElement(element)"
                  class="element-item"
                >
                  <i :class="element.icon"></i>
                  <span>{{ element.name }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Templates Tab -->
          <div v-if="activeSidebarTab === 'templates'" class="templates-panel">
            <div v-for="(templates, category) in templateCategories" :key="category" class="template-category">
              <h4>{{ category }}</h4>
              <div class="template-grid">
                <div 
                  v-for="template in templates" 
                  :key="template.id"
                  @click="insertTemplate(template)"
                  class="template-item"
                >
                  <img :src="template.thumbnail" :alt="template.name" />
                  <span>{{ template.name }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Blocks Tab -->
          <div v-if="activeSidebarTab === 'blocks'" class="blocks-panel">
            <button @click="showSaveBlockModal = true" class="btn-primary btn-block">
              <i class="fas fa-plus"></i> Save Current Selection
            </button>
            
            <div class="saved-blocks">
              <div 
                v-for="block in savedBlocks" 
                :key="block.id"
                draggable="true"
                @dragstart="startDragBlock(block)"
                class="block-item"
              >
                <img :src="block.thumbnail" v-if="block.thumbnail" />
                <div class="block-info">
                  <span>{{ block.name }}</span>
                  <button @click="deleteBlock(block)" class="btn-delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Canvas Area -->
      <div class="pb-canvas-wrapper" :style="canvasStyle">
        <div class="pb-canvas" ref="canvas">
          <div 
            v-if="sections.length === 0" 
            class="empty-canvas"
            @drop="handleDropToCanvas"
            @dragover.prevent
            @dragenter.prevent
          >
            <i class="fas fa-plus-circle"></i>
            <p>Drag elements here to start building</p>
          </div>

          <section-component
            v-for="(section, index) in sections"
            :key="section.id"
            :section="section"
            :index="index"
            @update="updateSection"
            @delete="deleteSection"
            @duplicate="duplicateSection"
            @move-up="moveSectionUp"
            @move-down="moveSectionDown"
            @select="selectElement"
            :selected="selectedElement?.id === section.id"
            :preview-mode="previewMode"
          />

          <!-- Add Section Button -->
          <div class="add-section-btn" @click="addNewSection" v-if="!previewMode">
            <i class="fas fa-plus"></i> Add Section
          </div>
        </div>
      </div>

      <!-- Right Sidebar - Properties Panel -->
      <div class="pb-properties" v-if="selectedElement && !previewMode">
        <div class="properties-header">
          <h3>{{ selectedElement.type }} Settings</h3>
          <button @click="selectedElement = null" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="properties-tabs">
          <button 
            v-for="tab in propertyTabs" 
            :key="tab.id"
            @click="activePropertyTab = tab.id"
            :class="{ active: activePropertyTab === tab.id }"
          >
            {{ tab.name }}
          </button>
        </div>

        <div class="properties-content">
          <properties-panel
            :element="selectedElement"
            :tab="activePropertyTab"
            @update="updateElementProperties"
          />
        </div>
      </div>
    </div>

    <!-- Modals -->
    <revisions-modal 
      v-if="showRevisions"
      :page-id="pageId"
      @close="showRevisions = false"
      @restore="restoreRevision"
    />

    <save-block-modal
      v-if="showSaveBlockModal"
      @close="showSaveBlockModal = false"
      @save="saveBlock"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import SectionComponent from './components/SectionComponent.vue';
import PropertiesPanel from './components/PropertiesPanel.vue';
import RevisionsModal from './components/RevisionsModal.vue';
import SaveBlockModal from './components/SaveBlockModal.vue';
import { usePageBuilder } from './composables/usePageBuilder';
import { useDragDrop } from './composables/useDragDrop';

export default {
  name: 'PageBuilder',
  components: {
    SectionComponent,
    PropertiesPanel,
    RevisionsModal,
    SaveBlockModal
  },
  props: {
    pageId: {
      type: [String, Number],
      default: null
    },
    initialData: {
      type: Object,
      default: () => ({})
    },
    elementTypes: {
      type: Object,
      default: () => ({})
    },
    templates: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const {
      sections,
      selectedElement,
      pageTitle,
      pageStatus,
      saving,
      addSection,
      updateSection,
      deleteSection,
      duplicateSection,
      moveSectionUp,
      moveSectionDown,
      savePage,
      publishPage,
      loadPage
    } = usePageBuilder(props.pageId);

    const {
      startDragElement,
      startDragBlock,
      handleDropToCanvas
    } = useDragDrop(sections, addSection);

    // UI State
    const sidebarCollapsed = ref(false);
    const activeSidebarTab = ref('elements');
    const activePropertyTab = ref('content');
    const previewMode = ref(false);
    const currentDevice = ref('desktop');
    const elementSearch = ref('');
    const showRevisions = ref(false);
    const showSaveBlockModal = ref(false);
    const savedBlocks = ref([]);

    // Device configurations
    const devices = [
      { name: 'desktop', icon: 'fas fa-desktop', width: '100%' },
      { name: 'tablet', icon: 'fas fa-tablet-alt', width: '768px' },
      { name: 'mobile', icon: 'fas fa-mobile-alt', width: '375px' }
    ];

    const sidebarTabs = [
      { id: 'elements', name: 'Elements', icon: 'fas fa-cube' },
      { id: 'templates', name: 'Templates', icon: 'fas fa-layer-group' },
      { id: 'blocks', name: 'Blocks', icon: 'fas fa-th-large' }
    ];

    const propertyTabs = [
      { id: 'content', name: 'Content' },
      { id: 'style', name: 'Style' },
      { id: 'advanced', name: 'Advanced' }
    ];

    // Computed
    const filteredElements = computed(() => {
      if (!elementSearch.value) return props.elementTypes;
      
      const search = elementSearch.value.toLowerCase();
      const filtered = {};
      
      Object.entries(props.elementTypes).forEach(([category, elements]) => {
        const filteredElements = elements.filter(el => 
          el.name.toLowerCase().includes(search)
        );
        if (filteredElements.length > 0) {
          filtered[category] = filteredElements;
        }
      });
      
      return filtered;
    });

    const templateCategories = computed(() => {
      const categories = {};
      props.templates.forEach(template => {
        if (!categories[template.category]) {
          categories[template.category] = [];
        }
        categories[template.category].push(template);
      });
      return categories;
    });

    const canvasStyle = computed(() => {
      const device = devices.find(d => d.name === currentDevice.value);
      return {
        maxWidth: device.width,
        margin: '0 auto'
      };
    });

    // Methods
    const toggleSidebar = () => {
      sidebarCollapsed.value = !sidebarCollapsed.value;
    };

    const togglePreview = () => {
      previewMode.value = !previewMode.value;
      selectedElement.value = null;
    };

    const addNewSection = () => {
      addSection({
        type: 'row',
        columns: [
          {
            width: 12,
            elements: []
          }
        ]
      });
    };

    const insertTemplate = async (template) => {
      try {
        const response = await axios.get(`/api/page-builder/templates/${template.id}`);
        const content = response.data.content;
        
        content.forEach(section => {
          addSection(section);
        });
      } catch (error) {
        console.error('Error loading template:', error);
      }
    };

    const updatePageTitle = () => {
      // Auto-save page title
      if (props.pageId) {
        savePage();
      }
    };

    const selectElement = (element) => {
      selectedElement.value = element;
    };

    const updateElementProperties = (updates) => {
      if (selectedElement.value) {
        Object.assign(selectedElement.value, updates);
        savePage();
      }
    };

    const saveBlock = async (blockData) => {
      try {
        const response = await axios.post('/api/page-builder/blocks', {
          name: blockData.name,
          category: blockData.category,
          content: selectedElement.value,
          is_global: blockData.isGlobal
        });
        
        savedBlocks.value.push(response.data.block);
        showSaveBlockModal.value = false;
      } catch (error) {
        console.error('Error saving block:', error);
      }
    };

    const deleteBlock = async (block) => {
      if (confirm('Are you sure you want to delete this block?')) {
        try {
          await axios.delete(`/api/page-builder/blocks/${block.id}`);
          savedBlocks.value = savedBlocks.value.filter(b => b.id !== block.id);
        } catch (error) {
          console.error('Error deleting block:', error);
        }
      }
    };

    const restoreRevision = async (revisionId) => {
      try {
        await axios.post(`/api/page-builder/pages/${props.pageId}/revisions/${revisionId}/restore`);
        await loadPage();
        showRevisions.value = false;
      } catch (error) {
        console.error('Error restoring revision:', error);
      }
    };

    // Load saved blocks
    const loadSavedBlocks = async () => {
      try {
        const response = await axios.get('/api/page-builder/blocks');
        savedBlocks.value = response.data.blocks;
      } catch (error) {
        console.error('Error loading blocks:', error);
      }
    };

    // Lifecycle
    onMounted(() => {
      if (props.pageId) {
        loadPage();
      }
      loadSavedBlocks();
      
      // Auto-save every 60 seconds
      setInterval(() => {
        if (props.pageId && !saving.value) {
          savePage();
        }
      }, 60000);
    });

    // Keyboard shortcuts
    const handleKeyboard = (e) => {
      if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
          case 's':
            e.preventDefault();
            savePage();
            break;
          case 'z':
            e.preventDefault();
            // Undo functionality
            break;
          case 'y':
            e.preventDefault();
            // Redo functionality
            break;
        }
      }
    };

    onMounted(() => {
      document.addEventListener('keydown', handleKeyboard);
    });

    return {
      // Data
      sections,
      selectedElement,
      pageTitle,
      pageStatus,
      saving,
      sidebarCollapsed,
      activeSidebarTab,
      activePropertyTab,
      previewMode,
      currentDevice,
      elementSearch,
      showRevisions,
      showSaveBlockModal,
      savedBlocks,
      devices,
      sidebarTabs,
      propertyTabs,
      
      // Computed
      filteredElements,
      templateCategories,
      canvasStyle,
      
      // Methods
      toggleSidebar,
      togglePreview,
      addNewSection,
      insertTemplate,
      updatePageTitle,
      selectElement,
      updateElementProperties,
      saveBlock,
      deleteBlock,
      restoreRevision,
      updateSection,
      deleteSection,
      duplicateSection,
      moveSectionUp,
      moveSectionDown,
      savePage,
      publishPage,
      startDragElement,
      startDragBlock,
      handleDropToCanvas
    };
  }
};
</script>

<style lang="scss">
.page-builder {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f5f5f5;

  .pb-toolbar {
    height: 60px;
    background: white;
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);

    .toolbar-left {
      display: flex;
      align-items: center;
      gap: 20px;

      .btn-icon {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #666;
        
        &:hover {
          color: #333;
        }
      }

      .page-title input {
        border: none;
        font-size: 18px;
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 4px;
        
        &:hover {
          background: #f5f5f5;
        }
        
        &:focus {
          outline: none;
          background: #f5f5f5;
        }
      }
    }

    .toolbar-center {
      .device-switcher {
        display: flex;
        gap: 5px;
        background: #f5f5f5;
        padding: 5px;
        border-radius: 6px;

        .device-btn {
          background: none;
          border: none;
          padding: 8px 12px;
          cursor: pointer;
          border-radius: 4px;
          color: #666;
          
          &:hover {
            background: #e0e0e0;
          }
          
          &.active {
            background: white;
            color: #007cba;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
          }
        }
      }
    }

    .toolbar-right {
      display: flex;
      gap: 10px;

      button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;

        &.btn-primary {
          background: #007cba;
          color: white;
          
          &:hover:not(:disabled) {
            background: #005a87;
          }
        }

        &.btn-secondary {
          background: #f0f0f0;
          color: #333;
          
          &:hover {
            background: #e0e0e0;
          }
        }

        &.btn-success {
          background: #46b450;
          color: white;
          
          &:hover {
            background: #3a9444;
          }
        }

        &:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }
      }
    }
  }

  .pb-main {
    flex: 1;
    display: flex;
    overflow: hidden;

    .pb-sidebar {
      width: 300px;
      background: white;
      border-right: 1px solid #ddd;
      display: flex;
      flex-direction: column;
      transition: width 0.3s;

      &.collapsed {
        width: 60px;

        .sidebar-tabs {
          .tab-btn span {
            display: none;
          }
        }

        .sidebar-content {
          display: none;
        }
      }

      .sidebar-tabs {
        display: flex;
        border-bottom: 1px solid #ddd;

        .tab-btn {
          flex: 1;
          padding: 12px;
          background: none;
          border: none;
          border-bottom: 2px solid transparent;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          color: #666;

          &:hover {
            background: #f5f5f5;
          }

          &.active {
            color: #007cba;
            border-bottom-color: #007cba;
          }
        }
      }

      .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 20px;

        .search-box {
          margin-bottom: 20px;

          input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;

            &:focus {
              outline: none;
              border-color: #007cba;
            }
          }
        }

        .element-category {
          margin-bottom: 30px;

          h4 {
            margin: 0 0 15px;
            font-size: 14px;
            text-transform: uppercase;
            color: #666;
            font-weight: 600;
          }

          .element-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;

            .element-item {
              background: #f5f5f5;
              border: 1px solid #ddd;
              border-radius: 4px;
              padding: 15px 10px;
              text-align: center;
              cursor: move;
              transition: all 0.2s;

              &:hover {
                background: #007cba;
                color: white;
                border-color: #007cba;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
              }

              i {
                display: block;
                font-size: 24px;
                margin-bottom: 8px;
              }

              span {
                font-size: 12px;
              }
            }
          }
        }
      }
    }

    .pb-canvas-wrapper {
      flex: 1;
      overflow: auto;
      padding: 20px;
      background: #f5f5f5;

      .pb-canvas {
        min-height: 100%;
        background: white;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        position: relative;

        .empty-canvas {
          padding: 100px;
          text-align: center;
          color: #999;
          border: 2px dashed #ddd;
          margin: 20px;
          border-radius: 8px;

          i {
            font-size: 48px;
            margin-bottom: 20px;
          }

          p {
            font-size: 18px;
          }
        }

        .add-section-btn {
          text-align: center;
          padding: 20px;
          cursor: pointer;
          color: #007cba;
          transition: all 0.2s;

          &:hover {
            background: #f5f5f5;
            color: #005a87;
          }
        }
      }
    }

    .pb-properties {
      width: 320px;
      background: white;
      border-left: 1px solid #ddd;
      display: flex;
      flex-direction: column;

      .properties-header {
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;

        h3 {
          margin: 0;
          font-size: 16px;
        }

        .btn-close {
          background: none;
          border: none;
          font-size: 18px;
          cursor: pointer;
          color: #666;

          &:hover {
            color: #333;
          }
        }
      }

      .properties-tabs {
        display: flex;
        border-bottom: 1px solid #ddd;

        button {
          flex: 1;
          padding: 12px;
          background: none;
          border: none;
          border-bottom: 2px solid transparent;
          cursor: pointer;
          color: #666;

          &:hover {
            background: #f5f5f5;
          }

          &.active {
            color: #007cba;
            border-bottom-color: #007cba;
          }
        }
      }

      .properties-content {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
      }
    }
  }

  &.preview-mode {
    .pb-canvas {
      .section-component {
        .section-controls,
        .column-controls,
        .element-controls {
          display: none !important;
        }
      }
    }
  }
}
</style>