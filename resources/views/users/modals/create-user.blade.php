<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="addUserForm" class="modal-content" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5>Create User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6 mb-3">
                    <label>Name</label>
                    <input type="text" id="name" class="form-control" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" id="email" class="form-control" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Password</label>
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_department">Department</label>
                    <select id="edit_department" name="department" class="form-control" required>
                        <option value="" selected disabled>Select Department</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="IT">IT</option>
                        <option value="Marketing">Marketing</option>
                        <!-- Add more departments as needed -->
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select id="gender" class="form-control" name="gender" required>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date of Birth</label>
                    <input type="text" id="dob" class="form-control" name="date_of_birth_ad" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Phone</label>
                    <input type="text" id="phone" class="form-control" name="phone_number" required  pattern="[0-9]{10}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date of Joining</label>
                    <input type="text" id="joining" class="form-control" name="date_of_joining" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Image</label>
                    <input type="file" id="image" class="form-control" name="image" accept="image/*">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Address</label>
                    <textarea id="address" class="form-control" name="address" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>