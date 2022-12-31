<script setup>
    import axios from 'axios';
    import moment from 'moment';
    import { ref } from 'vue';
    import { useToastr } from '../../toastr.js';

    const toastr = useToastr();

    defineProps({
        role: Object,
        index: Number,
    });

    const emit = defineEmits(['roleDeleted', 'editRole']);

    const roleIdBeingDeleted = ref(null);

     const confirmRoleDeletion = (role) => {
        roleIdBeingDeleted.value = role.id;
        $('#deleteRoleModal').modal('show');
    }

    const deleteRole = () => {
        axios.delete(`/api/roles/${roleIdBeingDeleted.value}`)
        .then(() => {
            $('#deleteRoleModal').modal('hide');
            toastr.success('Role delete successfully.!');
            emit('roleDeleted', roleIdBeingDeleted.value);
        });
    }

    const statues = ref([
        {
            name: 'Active',
            value: 1
        },
        {
            name: 'InActive',
            value: 0
        }
    ]);

    const changeStatus = (role, status) => {
        axios.patch(`/api/roles/${role.id}/change-status`, {
            status: status,
        }).then(() => {
            toastr.success('Status changed successfully!');
        })
    }
</script>
<template>
    <tr>
        <td>{{ index + 1 }}.</td>
        <td>{{ role.name }}</td>
        <td>{{ role.description }}</td>
        <td>{{ moment(role.created_at).format('DD-MM-YYYY') }}</td>
        <td>
            <select class="form-control" @change="changeStatus(role, $event.target.value)">
                <option v-for="status in statues" :key="status.id" :value="status.value" :selected="role.status===status.value">{{ status.name }}</option>
            </select>
        </td>
        <td>
            <a href="#" @click.prevent="$emit('editRole', role)"><i class="fa fa-edit"></i></a>
            <a href="#" @click.prevent="confirmRoleDeletion(role)"><i class="fa fa-trash text-danger ml-2"></i></a>
        </td>
    </tr>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Delete Role
                    </h5>
                </div>
                <div class="modal-body">
                    <h5>Ary you sure you want to delete this role ?</h5>
                </div>
                <div class="modal-footer">
                    <button @click.prevent="deleteRole" type="button" class="btn btn-primary">Delete Role</button>
                </div>
            </div>
        </div>
    </div>
</template>
