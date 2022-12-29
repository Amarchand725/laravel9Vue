import Dashboard from './components/dashboard.vue';
import Appointments from './pages/appointments/index.vue';
import Users from './pages/users/index.vue';
import UpdateSettings from './pages/settings/updatesettings.vue';
import UpdateProfile from './pages/profile/updateprofile.vue';

export default[
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {
        path: '/admin/appointments',
        name: 'admin.appointments',
        component: Appointments,
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: Users,
    },
    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: UpdateSettings,
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: UpdateProfile,
    }
]
