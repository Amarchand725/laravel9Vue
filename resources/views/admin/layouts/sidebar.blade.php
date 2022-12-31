<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <router-link to="/admin/dashboard" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
                Dashboard
            </p>
            </router-link>
        </li>
        <li class="nav-item">
            <router-link to="/admin/roles" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-tasks"></i>
            <p>
                Roles
            </p>
            </router-link>
        </li>
        <li class="nav-item">
            <router-link to="/admin/users" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
                Users
            </p>
            </router-link>
        </li>
        <li class="nav-item">
            <router-link to="/admin/settings" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-cog"></i>
            <p>
                Settings
            </p>
            </router-link>
        </li>
        <li class="nav-item">
            <router-link to="/admin/profile" active-class="active" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>
                Profile
            </p>
            </router-link>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>
                    Logout
                </p>
            </a>
        </li>
    </ul>
  </nav>
