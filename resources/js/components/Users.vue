<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">


            <!--Modal-->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users Table</h3>

                        <div class="card-tools">

                            <button class="btn btn-success" @click="newModal()"> Add new <i
                                    class="fas fa-user-plus fa-fw"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered at</th>
                                <th>Modify</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr v-for="user in users.data" :key="user.id">
                                <td> {{user.id}}</td>
                                <td> {{user.name }}</td>
                                <td> {{user.email}}</td>
                                <td> {{user.type | upText }}</td>
                                <td> {{user.created_at |myDate }}</td>

                                <td>
                                    <a href="#" @click="editModal(user)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    /
                                    <a href="#" @click="deleteUser(user.id)">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination :data="users" @pagination-change-page="getResults"></pagination>

                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>


        <div v-if="!$gate.isAdminOrAuthor()"> <!--if the user is not admin display not found page-->
            <not-found></not-found>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editmode" class="modal-title" id="addNewLabel">Add new</h5>
                        <h5 v-show="editmode" class="modal-title" id="addNewLabel">Update user info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>


                    <!-- @submit.prevent this will not refresh the page on submit-->
                    <!-- if the edit mode is true do the update function-->
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                        <!-- this a function we wrote it below in the script -->
                        <div class="modal-body">
                            <!--the form-->
                            <div class="form-group">
                                <!--model binding-->
                                <input v-model="form.name" type="text" name="name" placeholder="Name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>

                            <div class="form-group">
                                <input v-model="form.email" type="email" name="email"
                                       placeholder="Email Address"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>

                            <div class="form-group">
                        <textarea v-model="form.bio" name="bio" id="bio"
                                  placeholder="Short bio for user (Optional)"
                                  class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                <has-error :form="form" field="bio"></has-error>
                            </div>


                            <div class="form-group">
                                <select name="type" v-model="form.type" id="type" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option selected disabled>Select User Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard User</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>

                            <div class="form-group">
                                <input v-model="form.password" type="password" name="password" id="password"
                                       placeholder="Enter your password" class="form-control"
                                       :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>


                                <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                                <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>


</template>

<script>
    export default {
        data() {
            return {
                editmode: false,//if its true display the edit function
                users: {}, //object
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''

                })
            }
        },
        methods: {
            // for paginating
            getResults(page = 1) {
                axios.get('api/user?page=' + page)
                    .then(response => {
                        this.users = response.data;
                    });
            }
            ,
            newModal() {
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');//show the modal after the creation of the  user
                // data-toggle="modal" data-target="#addNew"

            },
            editModal(user) {
                this.editmode = true;
                // console.log(user);
                this.form.reset();
                $('#addNew').modal('show');//show the modal after the creation of the  user
                this.form.fill(user);

            },
            loadUsers() {
                if (this.$gate.isAdminOrAuthor()) {
                    axios.get('api/user').then(({data}) => (this.users = data));
                }
            },
            createUser() {
                this.$Progress.start(); // start the progress bar

                this.form.post('api/user')//send  post request to store data
                    .then(() => {
                        Fire.$emit('AfterCreate'); //fire a specific event
                        $('#addNew').modal('hide');//hide the modal after the creation of the  user

                        Toast.fire({
                            type: 'success',
                            title: 'user created  successfully'
                        });//swwet alert after the creation of the user
                        this.$Progress.finish();// finish the progress bar
                    })//when the validation is success
                    .catch(() => {
                    });
                //catch the error and display it


            },
            deleteUser(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    if (result.value) { // if he pressed the yes
                        //Send ajax request to serve
                        this.form.delete('api/user/' + id).then(() => {

                            Swal.fire(
                                'Deleted!',
                                'The user has been deleted.',
                                'success'
                            );
                            Fire.$emit('AfterCreate'); //fire a specific event


                        }).catch(() => {
                            Swal.fire(
                                'Failed!', 'Somthing went wrong', 'warning');
                        });
                    }

                })
            },
            updateUser() {
                // console.log(this.form.id);
                this.form.put('api/user/' + this.form.id)
                    .then(() => {
                        this.$Progress.start();
                        Swal.fire(
                            'Updated',
                            'The user has been updated.',
                            'success'
                        );
                        $('#addNew').modal('hide');//hide the modal after the creation of the  user
                        this.$Progress.finish();
                        Fire.$emit('AfterCreate'); //fire a specific event

                    }).catch(() => {
                    Swal.fire(
                        'Failed!', 'Somthing went wrong', 'warning');
                    this.$Progress.fail();

                });
            }
        },
        // once the user is created activate this function
        created() {

            Fire.$on('searching', () => {
                let query = this.$parent.search; //thats how we acess data feom the app.js the main
                axios.get('api/findUser?q=' + query)
                    .then((data) => {
                        this.users=data.data;
                    })
                    .catch(() => {

                    });
            }); //listen the event and load a function

            this.loadUsers();
            // () is like function()
            // setInterval( () => this.loadUsers(),3000); / activate this function every 3 second

            Fire.$on('AfterCreate', () => {
                this.loadUsers();
            }); //listen the event and load a function
        }
    }
</script>
