<script setup>
    import axios from 'axios';
    import { ref, onMounted, reactive, watch } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';
    import { useToastr } from '../../toastr.js';
    import UserRecords from './UserRecords.vue';
    import { debounce } from 'lodash';


    const toastr = useToastr();
    const users = ref([]);
    const roles = ref([]);
    const editing = ref(false);
    const formValues = ref();
    const form = ref(null);

    const getUsers = () => {
        axios.get('/api/users')
        .then((response) => {
            users.value = response.data.users;
            roles.value = response.data.roles;
        })
    }

    const createUserSchema = yup.object({
        role: yup.string().required(),
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().required().min(8),
    });

    const editUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().when((password, schema) => {
            return password ? schema.required().min(8) : schema;
        }),
    });

    const createUser = (values, { resetForm, setErrors }) =>{
        axios.post('/api/users', values)
        .then((response) => {
            users.value.unshift(response.data);
            $('#userFormModal').modal('hide');
            resetForm();
            toastr.success('User added successfully.!');
        }).catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        })
    }

    const addUser = () => {
        editing.value = false;
        formValues.value = {
            role: '',
            name: '',
            email: '',
        };
        $('#userFormModal').modal('show');
    }

    const editUser = (user) => {
        editing.value = true;
        form.value.resetForm();
        $('#userFormModal').modal('show');
        formValues.value = {
            id: user.id,
            name: user.name,
            email: user.email,
        };
    }

    const updateUser = (values, {setErrors}) => {
        axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.id );
            users.value[index] = response.data;
            $('#userFormModal').modal('hide');
            toastr.success('User updated successfully.!');
        }).catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        })
    }

    const handleSubmit = (values, actions) => {
        if(editing.value){
            updateUser(values, actions);
        }else{
            createUser(values, actions);
        }
    }

    const userDeleted = (userId) => {
        users.value = users.value.filter(user => user.id !== userId);
    }

    const searchQuery = ref(null);

    const search = () => {
        axios.get('/api/users/search', {
            params: {
                query: searchQuery.value
            }
        }).then(response => {
            users.value =response.data;
        })
    }

    watch(searchQuery , debounce(() => {
        search();
    }, 300))

    onMounted(()=>{
        getUsers();
    });
</script>

<template>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Users </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <button @click="addUser" type="button" class="mb-2 btn btn-primary">
                Add New User
            </button>
            <div>
                <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:10px">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered Date</th>
                            <th>Role</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody v-if="users.length > 0">
                        <UserRecords v-for="(user, index) in users"
                            :key="user.id"
                            :user=user
                            :index=index
                            :roles=roles
                            @user-deleted="userDeleted"
                            @edit-user="editUser"
                        />
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="6" class="text-center">No Results Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span v-if="editing"> Edit User </span>
                        <span v-else> Add New User </span>
                    </h5>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div v-if="!editing" class="form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <Field  as="select" name="role" id="role" class="form-control">
                                <option value="" selected>Select role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </Field>
                            <span class="invalid-feedback">{{ errors.role }}</span>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid':errors.name }" id="name" aria-describedby="" placeholder="Enter user name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <Field name="email" type="email" class="form-control" :class="{ 'is-invalid':errors.email }" id="email" aria-describedby="" placeholder="Enter email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password  <span v-if="!editing" class="text-danger">*</span></label>
                            <Field name="password" type="password" class="form-control" :class="{ 'is-invalid':errors.password }" id="password" aria-describedby="" placeholder="Enter password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>
