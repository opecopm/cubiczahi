<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <!-- Toggle Buttons -->
                <div class="mb-3 text-center">
                    <button type="button" class="btn btn-primary me-2" id="btnParent">Add Parent</button>
                    <button type="button" class="btn btn-outline-primary" id="btnChild">Add Subcategory</button>
                </div>

                <!-- Form -->
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" placeholder="Enter category name">
                    </div>

                    <!-- Parent select for subcategory -->
                    <div class="mb-3" id="parentSelectContainer" style="display:none;">
                        <label for="parentCategory" class="form-label">Select Parent Category</label>
                        <select class="form-select" id="parentCategory">
                            <option value="">-- Select Parent --</option>
                        </select>
                        <div id="noParentMessage" class="text-danger mt-1" style="display:none;">
                            No parent categories exist. Please add a parent category first.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categorySlug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="categorySlug" placeholder="Enter slug">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="categoryStatus" checked>
                        <label class="form-check-label" for="categoryStatus">Active</label>
                    </div>
                </form>

                <!-- Existing Categories -->
                <div class="mt-4">
                    <h6>Existing Categories:</h6>
                    <ul class="list-group" id="categoryList"></ul>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addCategory()">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    let categoryIdCounter = 1;
    let isChild = false;
    const categoryList = document.getElementById('categoryList');
    const parentSelect = document.getElementById('parentCategory');
    const noParentMessage = document.getElementById('noParentMessage');

    const btnParent = document.getElementById('btnParent');
    const btnChild = document.getElementById('btnChild');
    const parentSelectContainer = document.getElementById('parentSelectContainer');

    btnParent.addEventListener('click', () => {
        isChild = false;
        btnParent.classList.replace('btn-outline-primary', 'btn-primary');
        btnChild.classList.replace('btn-primary', 'btn-outline-primary');
        parentSelectContainer.style.display = 'none';
    });

    btnChild.addEventListener('click', () => {
        isChild = true;
        btnChild.classList.replace('btn-outline-primary', 'btn-primary');
        btnParent.classList.replace('btn-primary', 'btn-outline-primary');

        // Show parent select
        parentSelectContainer.style.display = 'block';

        // Show message if no parent categories
        if (parentSelect.options.length <= 1) {
            noParentMessage.style.display = 'block';
        } else {
            noParentMessage.style.display = 'none';
        }
    });

    function addCategory() {
        const name = document.getElementById('categoryName').value.trim();
        const slug = document.getElementById('categorySlug').value.trim();
        const status = document.getElementById('categoryStatus').checked;
        const parentId = parentSelect.value;

        if (!name) {
            alert('Category name is required.');
            return;
        }

        if (isChild && parentSelect.options.length <= 1) {
            alert('Please create a parent category first.');
            return;
        }

        const newCategory = document.createElement('li');
        newCategory.classList.add('list-group-item');
        newCategory.textContent = name + (slug ? ` (slug: ${slug})` : '') + (status ? '' : ' [Inactive]');
        newCategory.dataset.id = categoryIdCounter++;

        if (isChild) {
            // append as subcategory
            const parentLi = document.querySelector(`#categoryList li[data-id='${parentId}'] > ul`);
            parentLi.appendChild(newCategory);
        } else {
            // append as parent
            const ul = document.createElement('ul');
            ul.classList.add('list-group', 'mt-2');
            newCategory.appendChild(ul);
            categoryList.appendChild(newCategory);

            // Add to parent select dropdown
            const option = document.createElement('option');
            option.value = newCategory.dataset.id;
            option.textContent = name;
            parentSelect.appendChild(option);
        }

        // Reset form
        document.getElementById('addCategoryForm').reset();
        noParentMessage.style.display = 'none';
        parentSelect.value = '';
    }
</script>
