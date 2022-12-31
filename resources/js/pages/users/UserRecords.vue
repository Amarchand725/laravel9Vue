<script setup>
    import axios from 'axios';
import moment from 'moment';
    import { ref } from 'vue';
    import { useToastr } from '../../toastr.js';

    const toastr = useToastr();

    defineProps({
        user: Object,
        index: Number,
        roles:Array
    });

    const emit = defineEmits(['userDeleted', 'editUser']);

    const userIdBeingDeleted = ref(null);

     const confirmUserDeletion = (user) => {
        userIdBeingDeleted.value = user.id;
        $('#deleteUserModal').modal('show');
    }

    const deleteUser = () => {
        axios.delete(`/api/users/${userIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteUserModal').modal('hide');
            toastr.success('User delete successfully.!');
            emit('userDeleted', userIdBeingDeleted.value);
        });
    }

    const changeRole = (user, role) => {
        axios.patch(`/api/users/${user.id}/change-role`, {
            role: role,
        }).then(() => {
            toastr.success('Role changes successfully!');
        })
    }
</script>
<template>
    <tr>
        <td>{{ index + 1 }}.</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ moment(user.created_at).format('DD-MM-YYYY') }}</td>
        <td>
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :key="role.id" :value="role.value" :selected="user.roles[0].name===role.name">{{ role.name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="confirmUserDeletion(user)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Delete User
                    </h5>
                </div>
                <div class="modal-body">
                    <h5>Ary you sure you want to delete this user ?</h5>
                </div>
                <div class="modal-footer">
                    <button @click.prevent="deleteUser" type="button" class="btn btn-primary">Delete User</button>
                </div>
            </div>
        </div>
    </div>
</template>
