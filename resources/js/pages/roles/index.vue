<script setup>
    import axios from 'axios';
    import { ref, onMounted, reactive, watch } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';
    import { useToastr } from '../../toastr.js';
    import RoleRecords from './RoleRecords.vue';
    import { debounce } from 'lodash';

    const toastr = useToastr();
    const roles = ref([]);
    const editing = ref(false);
    const formValues = ref();
    const form = ref(null);

    const getRoles = () => {
        axios.get('/api/roles')
        .then((response) => {
            roles.value = response.data.roles;
        })
    }

    const createRoleSchema = yup.object({
        name: yup.string().required(),
    });

    const editRoleSchema = yup.object({
        name: yup.string().required(),
    });

    const createRole = (values, { resetForm, setErrors }) =>{
        console.log(values);
        axios.post('/api/roles', values)
        .then((response) => {
            roles.value.unshift(response.data);
            $('#roleFormModal').modal('hide');
            resetForm();
            toastr.success('Role added successfully.!');
        }).catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        })
    }

    const addRole = () => {
        editing.value = false;
        formValues.value = {
            name: '',
            description: '',
        };
        $('#roleFormModal').modal('show');
    }

    const editRole = (role) => {
        editing.value = true;
        form.value.resetForm();
        $('#roleFormModal').modal('show');
        formValues.value = {
            id: role.id,
            name: role.name,
            description: role.description,
        };
    }

    const updateRole = (values, {setErrors}) => {
        axios.put('/api/roles/' + formValues.value.id, values)
        .then((response) => {
            const index = roles.value.findIndex(role => role.id === response.data.id );
            roles.value[index] = response.data;
            $('#roleFormModal').modal('hide');
            toastr.success('Role updated successfully.!');
        }).catch((error) => {
            if(error.response.data.errors){
                setErrors(error.response.data.errors);
            }
        })
    }

    const handleSubmit = (values, actions) => {
        if(editing.value){
            updateRole(values, actions);
        }else{
            createRole(values, actions);
        }
    }

    const roleDeleted = (roleId) => {
        roles.value = roles.value.filter(role => role.id !== roleId);
    }

    const searchQuery = ref(null);

    const search = () => {
        axios.get('/api/roles/search', {
            params: {
                query: searchQuery.value
            }
        }).then(response => {
            roles.value =response.data;
        })
    }

    watch(searchQuery , debounce(() => {
        search();
    }, 300))

    onMounted(()=>{
        getRoles();
    });
</script>
<template>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Roles </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Roles</li>
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
            <button @click="addRole" type="button" class="mb-2 btn btn-primary">
                Add New Role
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
                            <th>Role</th>
                            <th>Description</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody v-if="roles.length > 0">
                        <RoleRecords v-for="(role, index) in roles"
                            :key="role.id"
                            :role=role
                            :index=index
                            @role-deleted="roleDeleted"
                            @edit-role="editRole"
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
    <div class="modal fade" id="roleFormModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span v-if="editing"> Edit Role </span>
                        <span v-else> Add New Role </span>
                    </h5>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editRoleSchema : createRoleSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <Field name="name" type="text" class="form-control" :class="{ 'is-invalid':errors.name }" id="name" aria-describedby="" placeholder="Enter role" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <!-- <Field name="description" type="text" class="form-control" id="description" aria-describedby="" placeholder="Enter short description" /> -->
                            <Field v-slot="{ field }" name="description" >
                                <textarea v-bind="field" class="form-control" id="description" aria-describedby="" placeholder="Enter short description" />
                            </Field>
                            <span class="invalid-feedback">{{ errors.description }}</span>
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
