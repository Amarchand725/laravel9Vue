<script setup>
    import axios from 'axios';
    import { ref, onMounted, reactive } from 'vue';

    const users = ref([]);

    const form = reactive({
        name: '',
        email: '',
        password: '',
    });

    const getUsers = () => {
        axios.get('/api/users')
        .then((response) => {
            users.value = response.data;
        })
    }

    const createUser = () => {
        axios.post('/api/users', form)
        .then((response) => {
            users.value.unshift(response.data);
            form.name = '';
            form.email = '';
            form.password = '';

            $('#create-user-modal').modal('hide');
        });
    }

    onMounted(()=>{
        getUsers();
    });
</script>

<template>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Users</h1>
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
        <button type="button" class="mb-2 btn btn-primary" data-toggle="modal" data-target="#create-user-modal">
            Add New User
        </button>
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
                    <tbody>
                        <tr v-for="(user, index) in users" :key="user.id">
                            <td>{{ index + 1 }}.</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="create-user-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input v-model="form.name" type="text" class="form-control" id="name" aria-describedby="" placeholder="Enter user name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input v-model="form.email" type="email" class="form-control" id="email" aria-describedby="" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input v-model="form.password" type="password" class="form-control" id="password" aria-describedby="" placeholder="Enter password">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button @click="createUser" type="button" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
