<?php

use CMW\Manager\Lang\LangManager;

function getMaxFileSizeInBytes(): int
{
    $size = ini_get('upload_max_filesize');
    $unit = strtoupper(substr($size, -1));
    $value = (int) $size;

    // fallthrough volontaire
    switch ($unit) {
        case 'G': $value *= 1024;
        case 'M': $value *= 1024;
        case 'K': $value *= 1024;
    }

    return $value;
}

$maxFileSize = getMaxFileSizeInBytes();
?>
<script>
    /*
* IMAGE DROPPER
* */
    document.querySelectorAll('.drop-img-area').forEach(initDropArea);

    function initDropArea(dropArea) {
        const inputName = dropArea.getAttribute('data-input-name') || 'fileInput';
        const imgAccept = dropArea.getAttribute('data-img-accept') || 'image/*';

        dropArea.classList.add('relative', 'border-4', 'border-dashed', 'border-gray-300', 'rounded-lg', 'py-4', 'px-2', 'flex', 'flex-col', 'items-center', 'justify-center', 'cursor-pointer');

        const fileInput = document.createElement('input');
        fileInput.id = 'fileElem';
        fileInput.type = 'file';
        fileInput.accept = imgAccept;
        fileInput.name = inputName;
        fileInput.hidden = true;
        dropArea.appendChild(fileInput);

        const instructionText = document.createElement('p');
        instructionText.textContent = '<?= LangManager::translate('core.imageDropper.fileDrop') ?>';
        dropArea.appendChild(instructionText);

        const errorMessage = document.createElement('div');
        errorMessage.id = 'error-message';
        errorMessage.textContent = '<?= LangManager::translate('core.imageDropper.fileFormat') ?>';
        errorMessage.classList.add('text-red-600', 'hidden');
        dropArea.appendChild(errorMessage);

        const defaultIcon = document.createElement('i');
        defaultIcon.id = 'default-icon';
        defaultIcon.className = 'text-6xl fa-regular fa-image mb-2';
        dropArea.insertBefore(defaultIcon, instructionText);

        let currentImage = null;
        let deleteButton = null;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.add('border-blue-500');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.remove('border-blue-500');
            }, false);
        });

        dropArea.addEventListener('click', () => fileInput.click());

        dropArea.addEventListener('drop', handleDrop);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        fileInput.addEventListener('change', (e) => {
            const files = e.target.files;
            handleFiles(files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const maxFileSize = <?= $maxFileSize ?>;

                if (files[0].size > maxFileSize) {
                    errorMessage.textContent = '<?= ini_get('upload_max_filesize') ?>o <?= LangManager::translate('core.imageDropper.fileSize') ?>';
                    errorMessage.classList.remove('hidden');
                    return;
                }

                if (files[0].type.startsWith('image/') && checkFileAccept(files[0], imgAccept)) {
                    errorMessage.classList.add('hidden');
                    fileInput.files = files;
                    previewFile(files[0]);
                } else {
                    errorMessage.textContent = '<?= LangManager::translate('core.imageDropper.fileFormat') ?>';
                    errorMessage.classList.remove('hidden');
                }
            }
        }

        function checkFileAccept(file, accept) {
            if (accept === 'image/*') return true;
            const acceptedTypes = accept.split(',').map(type => type.trim());
            return acceptedTypes.includes(file.type);
        }

        function previewFile(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                if (currentImage) {
                    dropArea.removeChild(currentImage);
                }
                if (deleteButton) {
                    dropArea.removeChild(deleteButton);
                }
                if (defaultIcon) {
                    defaultIcon.style.display = 'none';
                }

                const img = document.createElement('img');
                img.src = reader.result;
                img.classList.add('w-full', 'h-auto', 'object-cover', 'rounded-lg', 'mb-4');
                dropArea.insertBefore(img, instructionText);
                currentImage = img;

                deleteButton = document.createElement('button');
                deleteButton.innerHTML = '&times;';
                deleteButton.className = 'absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer';
                deleteButton.addEventListener('click', removeImage);
                dropArea.appendChild(deleteButton);
            }
        }

        function removeImage() {
            if (currentImage) {
                dropArea.removeChild(currentImage);
                currentImage = null;
            }
            if (deleteButton) {
                dropArea.removeChild(deleteButton);
                deleteButton = null;
            }
            if (defaultIcon) {
                defaultIcon.style.display = 'block';
            }
        }
    }
</script>

<script>
    /*
    * FILE DROPPER
    */
    document.querySelectorAll('.drop-file-area').forEach(initFileDropArea);

    function initFileDropArea(dropArea) {
        const inputName = dropArea.getAttribute('data-input-name') || 'fileInput';

        // Whitelist par défaut (MIME) si data-file-accept absent
        const defaultAcceptedMimes = [
            // Images
            'image/png','image/jpg','image/jpeg','image/gif','image/webp','image/x-icon','image/vnd.microsoft.icon','image/svg+xml',
            // Documents
            'application/pdf','text/plain','text/csv',
            // Microsoft Office
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            // LibreOffice / OpenDocument
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.presentation',
            // Design
            'image/vnd.adobe.photoshop','application/postscript','application/x-indesign',
            // Archives
            'application/zip','application/x-zip-compressed','application/x-tar','application/x-rar','application/vnd.rar',
            // Vidéos
            'video/mp4','video/webm','video/ogg',
            // Audio
            'audio/mpeg','audio/ogg','audio/wav','audio/webm'
        ];

        const fileAccept = dropArea.getAttribute('data-file-accept');
        const acceptValue = (fileAccept && fileAccept.trim().length)
            ? fileAccept.trim()
            : defaultAcceptedMimes.join(',');

        dropArea.classList.add(
            'relative','border-4','border-dashed','border-gray-300','rounded-lg',
            'py-4','px-2','flex','flex-col','items-center','justify-center','cursor-pointer'
        );

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = acceptValue;
        fileInput.name = inputName;
        fileInput.hidden = true;
        dropArea.appendChild(fileInput);

        const defaultIcon = document.createElement('i');
        defaultIcon.className = 'text-6xl fa-solid fa-file-arrow-up mb-2';
        dropArea.appendChild(defaultIcon);

        const instructionText = document.createElement('p');
        instructionText.textContent = '<?= LangManager::translate('core.fileDropper.fileDrop') ?>';
        dropArea.appendChild(instructionText);

        const errorMessage = document.createElement('div');
        errorMessage.textContent = '<?= LangManager::translate('core.fileDropper.fileFormat') ?>';
        errorMessage.classList.add('text-red-600','hidden','mt-2','text-center');
        dropArea.appendChild(errorMessage);

        let currentPreview = null;
        let deleteButton = null;

        ['dragenter','dragover','dragleave','drop'].forEach(evt =>
            dropArea.addEventListener(evt, preventDefaults, false)
        );

        function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

        ['dragenter','dragover'].forEach(evt =>
            dropArea.addEventListener(evt, () => dropArea.classList.add('border-blue-500'), false)
        );
        ['dragleave','drop'].forEach(evt =>
            dropArea.addEventListener(evt, () => dropArea.classList.remove('border-blue-500'), false)
        );

        dropArea.addEventListener('click', () => fileInput.click());
        dropArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
        fileInput.addEventListener('change', e => handleFiles(e.target.files));

        function normalizeAcceptList(accept) {
            return accept.split(',').map(s => s.trim()).filter(Boolean);
        }

        function isAccepted(file, accept) {
            // accepte "image/*"
            const list = normalizeAcceptList(accept);
            if (list.includes('image/*') && file.type.startsWith('image/')) return true;
            // accepte MIME exact
            if (list.includes(file.type)) return true;
            // accepte extensions (.pdf, .png, etc.)
            const lowerName = (file.name || '').toLowerCase();
            return list.some(a => a.startsWith('.') && lowerName.endsWith(a.toLowerCase()));
        }

        function setSingleFileToInput(file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
        }

        function clearInput() {
            const dt = new DataTransfer();
            fileInput.files = dt.files;
            fileInput.value = '';
        }

        function humanSize(bytes) {
            const units = ['B','KB','MB','GB','TB'];
            let i = 0, n = bytes;
            while (n >= 1024 && i < units.length - 1) { n /= 1024; i++; }
            return `${n.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
        }

        function handleFiles(files) {
            if (!files || !files.length) return;

            const file = files[0];
            const maxFileSize = <?= (int)$maxFileSize ?>;

            if (file.size > maxFileSize) {
                errorMessage.textContent = '<?= ini_get('upload_max_filesize') ?>o <?= LangManager::translate('core.fileDropper.fileSize') ?>';
                errorMessage.classList.remove('hidden');
                return;
            }

            if (!isAccepted(file, acceptValue)) {
                errorMessage.textContent = '<?= LangManager::translate('core.fileDropper.fileFormat') ?>';
                errorMessage.classList.remove('hidden');
                return;
            }

            errorMessage.classList.add('hidden');
            setSingleFileToInput(file);
            renderPreview(file);
        }

        function renderPreview(file) {
            removePreview();

            defaultIcon.style.display = 'none';

            // Image preview
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = () => {
                    const img = document.createElement('img');
                    img.src = reader.result;
                    img.classList.add('w-full','h-auto','object-cover','rounded-lg','mb-4');
                    dropArea.insertBefore(img, instructionText);
                    currentPreview = img;
                    addDeleteButton();
                };
                return;
            }

            // Generic file preview (icon + name + size)
            const box = document.createElement('div');
            box.classList.add('w-full','rounded-lg','border','border-gray-200','p-3','mb-4','flex','items-center','gap-3');

            const icon = document.createElement('i');
            icon.className = 'fa-solid fa-file text-2xl';
            box.appendChild(icon);

            const info = document.createElement('div');
            info.classList.add('flex','flex-col','min-w-0');

            const name = document.createElement('div');
            name.classList.add('font-semibold','truncate','max-w-full');
            name.textContent = file.name;

            const meta = document.createElement('div');
            meta.classList.add('text-sm','text-gray-500');
            meta.textContent = `${humanSize(file.size)}${file.type ? ' • ' + file.type : ''}`;

            info.appendChild(name);
            info.appendChild(meta);
            box.appendChild(info);

            dropArea.insertBefore(box, instructionText);
            currentPreview = box;

            addDeleteButton();
        }

        function addDeleteButton() {
            deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.innerHTML = '&times;';
            deleteButton.className = 'absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer';
            deleteButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                removePreview();
                clearInput();
            });
            dropArea.appendChild(deleteButton);
        }

        function removePreview() {
            if (currentPreview) {
                dropArea.removeChild(currentPreview);
                currentPreview = null;
            }
            if (deleteButton) {
                dropArea.removeChild(deleteButton);
                deleteButton = null;
            }
            defaultIcon.style.display = 'block';
        }
    }
</script>
