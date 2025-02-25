// $(document).ready(function () {
//     console.log('Role Permission Manage JS Loaded [updated]');
//     let permissionsData = [];
//     let permissionsOfRole = [];

//     // Fetch all permissions via AJAX and load them
//     function loadPermissions(callback) {
//         console.log('Loading Permissions');

//         //remove the permissions Container
//         $('#permissions-container').empty();

//         //unset the permissionsData
//         permissionsData = [];

//         $.ajax({
//             url: '/api/app/permission/get/all',
//             method: 'POST',
//             data: { search: query },
//             success: function (response) {
//                 // Process the response and group permissions by category
//                 permissionsData = response.map(permission => ({
//                     id: permission._id.$oid,
//                     name: permission.permission_name,
//                     category: permission.permission_category,
//                     description: permission.description
//                 }));

//                 // Call the callback function after loading permissions
//                 if (callback) {
//                     callback();
//                 }
//             },
//             error: function (error) {
//                 console.error("Error fetching permissions:", error);
//             }
//         });
//     }

//     function loadRolePermissions(role) {
//         $.ajax({
//             url: '/api/app/permission/get/by/role',
//             method: 'POST',
//             data: { roleId: role },
//             success: function (response) {
//                 // Check if the response indicates success and contains permissions
//                 if (response.success && Array.isArray(response.permission)) {
//                     console.log('Raw Response:', response);

//                     // Process the permissions array
//                     const newPermissions = response.permission.map(permission => ({
//                         id: permission.id,
//                         name: permission.name,
//                         category: permission.category,
//                         description: permission.description
//                     }));

//                     // Load all permissions after receiving role permissions
//                     loadPermissions(function() {
//                         // Merge new permissions with existing permissionsOfRole
//                         permissionsOfRole = [...permissionsOfRole, ...newPermissions]; 
//                         generatePermissions();
//                     });

//                 } else {
//                     console.error('Invalid response structure:', response);
//                 }
//             },
//             error: function (error) {
//                 // Handle errors

//                 switch (error.status) {
//                     case 404:
//                         new Toast("Information", "info", "No Permissions Found for the Role").show();
//                         break;
            
//                     case 500:
//                         new Toast("Error", "error", "Server Error. Please try again later.").show();
//                         break;
            
//                     default:
//                         const errorMessage = error.responseJSON && error.responseJSON.message 
//                             ? error.responseJSON.message 
//                             : "An unexpected error occurred. Please check your connection or try again.";
//                         new Toast("Error", "error", errorMessage).show();
//                         break;
//                 }
//             }
//         });
//     }

//     // Function to group permissions by category and render them
//     function generatePermissions() {
//         // Assuming `permissionsOfRole` contains an array of permission IDs
//         const selectedPermissions = permissionsOfRole.map(permission => permission.id);

//         // Group permissions by category
//         const groupedPermissions = permissionsData.reduce((acc, permission) => {
//             if (!acc[permission.category]) acc[permission.category] = [];
//             acc[permission.category].push(permission);
//             return acc;
//         }, {});

//         const container = $('#permissions-container');
//         container.empty(); // Clear the container

//         // Loop through grouped permissions
//         for (const category in groupedPermissions) {
//             const categoryGroup = groupedPermissions[category];

//             // Create category header with nested permissions
//             const categoryHeader = `
//                 <div class="mb-3">
//                     <ul class="list-unstyled">
//                         <li>
//                             <button type="button" class="btn w-100 text-start" data-bs-toggle="collapse" data-bs-target="#category-${category}">
//                                 <i class="fas fa-chevron-right" id="arrow-${category}"></i> ${category}
//                             </button>
                            
//                             <div id="category-${category}" class="collapse mb-3 show">
//                                 <ul class="list-unstyled ms-4">
//                                     ${categoryGroup.map(permission => {
//                 const isChecked = selectedPermissions.includes(permission.id) ? 'checked' : '';
//                 return `
//                                             <li>
//                                                 <div class="form-check">
//                                                     <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
//                                                         value="${permission.id}" id="perm-${permission.id}" ${isChecked}>
//                                                     <label class="form-check-label" for="perm-${permission.id}" data-bs-toggle="tooltip" 
//                                                         data-bs-placement="right" title="${permission.description}">
//                                                         ${permission.name}
//                                                     </label>
//                                                 </div>
//                                             </li>
//                                         `;
//             }).join('')}
//                                 </ul>
//                             </div>
//                         </li>
//                     </ul>
//                 </div>
//             `;

//             container.append(categoryHeader);
//         }

//         // Reinitialize tooltips for dynamically added elements
//         $('[data-bs-toggle="tooltip"]').tooltip();

//         container.on('click', '.select-all', function () {
//             const category = $(this).data('category');
//             const target = $(`#category-${category}`);
//             target.collapse('show'); // Expand the category
//             target.find('.permission-checkbox').prop('checked', true);
//         });

//         container.on('click', '.deselect-all', function () {
//             const category = $(this).data('category');
//             const target = $(`#category-${category}`);
//             target.find('.permission-checkbox').prop('checked', false);
//         });

//         // Handle arrow icon direction on collapse
//         container.on('show.bs.collapse', function (event) {
//             const category = event.target.id.replace('category-', '');
//             $(`#arrow-${category}`).removeClass('fa-chevron-right').addClass('fa-chevron-down');
//         });

//         container.on('hide.bs.collapse', function (event) {
//             const category = event.target.id.replace('category-', '');
//             $(`#arrow-${category}`).removeClass('fa-chevron-down').addClass('fa-chevron-right');
//         });
//     }

//     // Search functionality
//     $('#role-permission-search').on('input', function () {
//         const query = $(this).val().toLowerCase();

//         // Iterate through each category
//         $('#permissions-container .mb-3').each(function () {
//             const category = $(this);
//             const permissions = category.find('.form-check');

//             // Check visibility of each permission
//             let hasVisiblePermissions = false;
//             permissions.each(function () {
//                 const label = $(this).find('label').text().toLowerCase();
//                 const isVisible = label.includes(query);
//                 $(this).toggle(isVisible); // Show or hide the permission
//                 if (isVisible) hasVisiblePermissions = true; // Mark if at least one permission is visible
//             });

//             // Show or hide the entire category based on its permissions
//             if (hasVisiblePermissions) {
//                 category.show(); // Show category
//                 category.find('.collapse').collapse('show'); // Expand category
//             } else {
//                 category.hide(); // Hide category
//             }
//         });
//     });

//     // Submit form handler
//      // Submit form handler
//      $('#rolePermissionForm').on('submit', function (event) {
//         event.preventDefault();

//         const role = $('#permission-role').val();

//         console.log(role);

//         // Retrieve all selected permissions
//         let selectedPermissions = $("input[name='permissions[]']:checked").map(function () {
//             return $(this).val(); // Get the value of the checked checkbox
//         }).get();

//         if (selectedPermissions.length === 0) {
//             selectedPermissions = [];
//         }

//         // Log or use the selected permissions
//         console.log("Selected Permissions:", selectedPermissions);

//         // Create FormData object
//         const formData = new FormData();
//         formData.append('roleId', role);

//         selectedPermissions.forEach(permission => {
//             formData.append('permissionsID[]', permission); // Use 'permissions[]' to match array structure
//         });

//         console.log(formData);

//         $.ajax({
//             url: '/api/app/permission/grant',
//             method: 'POST',
//             processData: false, // Prevent jQuery from transforming the data
//             contentType: false, // Allow FormData to set the correct Content-Type
//             data: formData,
//             success: function (response) {
//                 console.log("Permission mapping saved successfully:", response);
//                 // Display response JSON in a dialog

//                 if (response.message.length == 0) {
//                     var successDialog = new Dialog("Success Details", "Permission Removed"); // Pretty print JSON
//                     successDialog.setButtons([
//                         {
//                             "name": "Close",
//                             "class": "btn-primary",
//                             "onClick": function (event) {
//                                 $(event.data.modal).modal('hide');
//                             }
//                         }
//                     ]);
//                     successDialog.show();

//                     resetPermissionsForm();

//                     var successToast = new Toast("Now", "success", "Permissions removed successfully.");
//                     successToast.show();
//                     return;
//                 }


//                 var permissionNames = response.message.map(function (permission) {
//                     return permission.name;
//                 });

//                 // Create a dialog with the permission names
//                 var successDialog = new Dialog(
//                     "Success Details",
//                     `<h6>Current Permissions</h6>
//                      <ul>
//                          <li>${permissionNames.join('</li><li>')}</li>
//                      </ul>`
//                 );

//                 successDialog.setButtons([
//                     {
//                         "name": "Close",
//                         "class": "btn-primary",
//                         "onClick": function (event) {
//                             $(event.data.modal).modal('hide');
//                         }
//                     }
//                 ]);
//                 successDialog.show();

//                 resetPermissionsForm();

//                 // Show success toast
//                 var successToast = new Toast("Now", "success", "Permissions granted successfully.");
//                 successToast.show();
//             },
//             error: function (error) {
//                 console.error("Error saving mapping:", error);

//                 // Display error JSON in a dialog
//                 var errorDialog = new Dialog("Error Details", JSON.stringify(error.responseJSON || error, null, 2)); // Pretty print JSON
//                 errorDialog.setButtons([
//                     {
//                         "name": "Close",
//                         "class": "btn-primary",
//                         "onClick": function (event) {
//                             $(event.data.modal).modal('hide');
//                         }
//                     }
//                 ]);
//                 errorDialog.show();

//                 resetPermissionsForm()

//                 // Show error toast
//                 var errorToast = new Toast("Error", "error", "Error saving permission mapping.");
//                 errorToast.show();
//             }
//         });
//     });

//     // Load permissions on page load
//     $('#permission-role').on('change', function () {

//         console.log('Role Changed');

//         const role = $(this).val();
//         console.log('Role Changed to :' + role);

//         //remove the permissions Container
//         $('#permissions-container').empty();

//         // Load permissions for the selected role first
//         loadRolePermissions(role);
//     });

//     // Reset permissions form
//     function resetPermissionsForm() {
//         $('#rolePermissionForm').trigger('reset');

//         //remove the permissions Container
//         $('#permissions-container').empty();

//         //unset the permissionsOfRole
//         permissionsOfRole = [];
//         //unset the permissionsData
//         permissionsData = [];
//     }

// });