import { ref } from 'vue';

export function useDragDrop(sections, addSection) {
  const draggedElement = ref(null);
  const draggedBlock = ref(null);
  const draggedSection = ref(null);
  const dropTarget = ref(null);
  const dragOverElement = ref(null);

  const startDragElement = (element) => {
    draggedElement.value = element;
    draggedBlock.value = null;
    draggedSection.value = null;
  };

  const startDragBlock = (block) => {
    draggedBlock.value = block;
    draggedElement.value = null;
    draggedSection.value = null;
  };

  const startDragSection = (section, index) => {
    draggedSection.value = { section, index };
    draggedElement.value = null;
    draggedBlock.value = null;
  };

  const handleDragOver = (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    
    // Add visual feedback
    const target = e.target.closest('.drop-zone');
    if (target && target !== dragOverElement.value) {
      if (dragOverElement.value) {
        dragOverElement.value.classList.remove('drag-over');
      }
      target.classList.add('drag-over');
      dragOverElement.value = target;
    }
  };

  const handleDragLeave = (e) => {
    const target = e.target.closest('.drop-zone');
    if (target) {
      target.classList.remove('drag-over');
    }
  };

  const handleDropToCanvas = (e) => {
    e.preventDefault();
    e.stopPropagation();
    
    if (dragOverElement.value) {
      dragOverElement.value.classList.remove('drag-over');
    }

    if (draggedElement.value) {
      // Create new element from dragged element type
      const newElement = createElementFromType(draggedElement.value);
      
      // Determine where to drop
      const dropZone = e.target.closest('.drop-zone');
      if (dropZone) {
        const columnId = dropZone.dataset.columnId;
        const sectionId = dropZone.dataset.sectionId;
        const position = dropZone.dataset.position || 'end';
        
        addElementToColumn(sectionId, columnId, newElement, position);
      } else {
        // Create new section with element
        addSection({
          type: 'row',
          columns: [{
            width: 12,
            elements: [newElement]
          }]
        });
      }
    } else if (draggedBlock.value) {
      // Insert saved block
      insertBlock(draggedBlock.value);
    } else if (draggedSection.value) {
      // Reorder sections
      const dropIndex = parseInt(e.target.closest('.section-component')?.dataset.index || sections.value.length);
      moveSection(draggedSection.value.index, dropIndex);
    }

    // Reset drag state
    draggedElement.value = null;
    draggedBlock.value = null;
    draggedSection.value = null;
    dragOverElement.value = null;
  };

  const createElementFromType = (elementType) => {
    return {
      id: generateId(),
      type: elementType.name,
      content: elementType.default_settings?.content || {},
      settings: elementType.default_settings?.settings || {},
      animations: {},
      responsive_settings: {},
      is_visible: true
    };
  };

  const addElementToColumn = (sectionId, columnId, element, position) => {
    const section = sections.value.find(s => s.id === sectionId);
    if (section) {
      const column = section.columns.find(c => c.id === columnId);
      if (column) {
        if (position === 'start') {
          column.elements.unshift(element);
        } else if (typeof position === 'number') {
          column.elements.splice(position, 0, element);
        } else {
          column.elements.push(element);
        }
      }
    }
  };

  const insertBlock = async (block) => {
    try {
      const response = await fetch(`/api/page-builder/blocks/${block.id}`);
      const data = await response.json();
      
      // Insert block content as new sections
      data.content.forEach(section => {
        addSection(section);
      });
    } catch (error) {
      console.error('Error inserting block:', error);
    }
  };

  const moveSection = (fromIndex, toIndex) => {
    if (fromIndex === toIndex) return;
    
    const section = sections.value.splice(fromIndex, 1)[0];
    sections.value.splice(toIndex, 0, section);
    
    // Update order property
    sections.value.forEach((section, index) => {
      section.order = index;
    });
  };

  const generateId = () => {
    return 'el_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
  };

  // Drag and drop for elements within columns
  const handleElementDragStart = (e, element, columnId, elementIndex) => {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', JSON.stringify({
      type: 'element',
      element,
      columnId,
      elementIndex
    }));
  };

  const handleElementDrop = (e, targetColumnId, targetIndex) => {
    e.preventDefault();
    e.stopPropagation();
    
    try {
      const data = JSON.parse(e.dataTransfer.getData('text/plain'));
      
      if (data.type === 'element') {
        // Move element within or between columns
        moveElement(
          data.columnId,
          data.elementIndex,
          targetColumnId,
          targetIndex
        );
      }
    } catch (error) {
      console.error('Error handling element drop:', error);
    }
  };

  const moveElement = (fromColumnId, fromIndex, toColumnId, toIndex) => {
    const fromColumn = findColumnById(fromColumnId);
    const toColumn = findColumnById(toColumnId);
    
    if (fromColumn && toColumn) {
      const element = fromColumn.elements.splice(fromIndex, 1)[0];
      
      if (fromColumnId === toColumnId && toIndex > fromIndex) {
        toIndex--;
      }
      
      toColumn.elements.splice(toIndex, 0, element);
      
      // Update order
      fromColumn.elements.forEach((el, idx) => el.order = idx);
      toColumn.elements.forEach((el, idx) => el.order = idx);
    }
  };

  const findColumnById = (columnId) => {
    for (const section of sections.value) {
      const column = section.columns.find(c => c.id === columnId);
      if (column) return column;
    }
    return null;
  };

  // Column resizing
  const handleColumnResize = (sectionId, columnIndex, newWidth) => {
    const section = sections.value.find(s => s.id === sectionId);
    if (section && section.columns[columnIndex]) {
      const column = section.columns[columnIndex];
      const oldWidth = column.width;
      const widthDiff = newWidth - oldWidth;
      
      // Adjust current column
      column.width = newWidth;
      
      // Adjust next column if exists
      if (section.columns[columnIndex + 1]) {
        const nextColumn = section.columns[columnIndex + 1];
        nextColumn.width = Math.max(1, nextColumn.width - widthDiff);
      }
      
      // Ensure total width doesn't exceed 12
      const totalWidth = section.columns.reduce((sum, col) => sum + col.width, 0);
      if (totalWidth > 12) {
        const excess = totalWidth - 12;
        column.width -= excess;
      }
    }
  };

  return {
    draggedElement,
    draggedBlock,
    draggedSection,
    dropTarget,
    startDragElement,
    startDragBlock,
    startDragSection,
    handleDragOver,
    handleDragLeave,
    handleDropToCanvas,
    handleElementDragStart,
    handleElementDrop,
    handleColumnResize,
    moveSection,
    moveElement
  };
}