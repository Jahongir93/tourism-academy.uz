/*
 * WAF-safe submit — make forms pass a server Web Application Firewall.
 *
 * On submit, risky data is converted to plain base64 text so the WAF never
 * sees <img>/<script>/src= (XSS rules) or raw binary uploads:
 *   - rich-text  (.tinymce-editor / [data-richtext])  → value prefixed @@B64@@
 *   - file input ([data-waf-file], or any file input in a form that also has
 *     a .tinymce-editor) → hidden field "__wffile_<name>" = JSON{n,t,d}, and
 *     the real <input type=file> is disabled (no binary leaves the browser).
 *
 * The server middleware App\Http\Middleware\DecodeRichText restores both.
 * Include this AFTER tinymce.min.js.
 */
(function () {
    var MARKER = '@@B64@@';
    var FILE_PREFIX = '__wffile_';

    function utf8ToBase64(str) {
        return MARKER + btoa(unescape(encodeURIComponent(str)));
    }

    function readFileAsBase64(file) {
        return new Promise(function (resolve, reject) {
            var r = new FileReader();
            r.onload = function () { resolve(r.result.split(',')[1] || ''); };
            r.onerror = reject;
            r.readAsDataURL(file);
        });
    }

    function fileInputsFor(form) {
        var explicit = Array.prototype.slice.call(form.querySelectorAll('input[type="file"][data-waf-file]'));
        // If the form has a rich-text editor, treat all its file inputs as WAF-sensitive
        if (form.querySelector('.tinymce-editor, [data-richtext]')) {
            form.querySelectorAll('input[type="file"]').forEach(function (el) {
                if (explicit.indexOf(el) === -1 && el.name) explicit.push(el);
            });
        }
        return explicit.filter(function (el) {
            // skip inputs that upload themselves via AJAX (data-no-waf)
            if (el.hasAttribute('data-no-waf')) return false;
            return el.name && el.files && el.files.length;
        });
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.__wafDone) return;
        if (!form.querySelectorAll) return;

        var richFields = Array.prototype.slice.call(
            form.querySelectorAll('.tinymce-editor, [data-richtext]')
        );
        var fileInputs = fileInputsFor(form);

        if (!richFields.length && !fileInputs.length) return;

        // Flush TinyMCE content into textareas first
        if (window.tinymce && typeof tinymce.triggerSave === 'function') {
            tinymce.triggerSave();
        }

        // Encode rich-text (synchronous)
        richFields.forEach(function (el) {
            var val = el.value || '';
            if (val && val.indexOf(MARKER) !== 0) el.value = utf8ToBase64(val);
        });

        // No files → let the form submit normally
        if (!fileInputs.length) return;

        // Files need async encoding → hold the submit
        e.preventDefault();

        var jobs = [];
        fileInputs.forEach(function (input) {
            var file = input.files[0];
            jobs.push(
                readFileAsBase64(file).then(function (b64) {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = FILE_PREFIX + input.name;
                    hidden.value = JSON.stringify({ n: file.name, t: file.type || 'application/octet-stream', d: b64 });
                    form.appendChild(hidden);
                    input.disabled = true; // stop the raw binary from being sent
                })
            );
        });

        Promise.all(jobs).then(function () {
            form.__wafDone = true;
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }).catch(function () {
            // On any failure, fall back to a normal submit (re-enable inputs)
            fileInputs.forEach(function (i) { i.disabled = false; });
            form.__wafDone = true;
            form.submit();
        });
    }, true); // capture phase
})();
