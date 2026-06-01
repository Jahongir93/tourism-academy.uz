/*
 * WAF-safe submit: base64-wrap rich-text (TinyMCE) fields before the form
 * POSTs, so a server Web Application Firewall doesn't see <img>/<script>/
 * src= in the body and block the request with 403.
 *
 * The server middleware App\Http\Middleware\DecodeRichText decodes any
 * value beginning with the marker back to the original HTML.
 *
 * Include this AFTER tinymce.min.js on any page with .tinymce-editor.
 */
(function () {
    var MARKER = '@@B64@@';

    function utf8ToBase64(str) {
        return MARKER + btoa(unescape(encodeURIComponent(str)));
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.querySelectorAll) return;

        var fields = form.querySelectorAll('.tinymce-editor, textarea[data-waf-encode], [data-richtext]');
        if (!fields.length) return;

        // Flush TinyMCE content into the underlying textareas first
        if (window.tinymce && typeof tinymce.triggerSave === 'function') {
            tinymce.triggerSave();
        }

        fields.forEach(function (el) {
            var val = el.value || '';
            if (val && val.indexOf(MARKER) !== 0) {
                el.value = utf8ToBase64(val);
            }
        });
    }, true); // capture phase: run before the browser sends the form
})();
