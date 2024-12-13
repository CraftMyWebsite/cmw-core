<?php

use CMW\Manager\Lang\LangManager;

function getMaxFileSizeInBytes(): int
{
    $size = ini_get('upload_max_filesize');
    $unit = strtoupper(substr($size, -1));
    $size = (int) $size;
    switch ($unit) {
        case 'G': $size *= 1024; // Gigabytes to Megabytes
        case 'M': $size *= 1024; // Megabytes to Kilobytes
        case 'K': $size *= 1024; // Kilobytes to Bytes
    }
    return $size;
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
        defaultIcon.className = 'text-6xl fa-solid fa-cloud-arrow-up mb-2';
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