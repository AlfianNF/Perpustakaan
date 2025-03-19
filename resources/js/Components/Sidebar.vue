<template>
    <aside class="bg-gray-800 text-white w-64 min-h-screen">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Admin Panel</h2>
            <ul>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin"
                        class="block hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                        >Dashboard</router-link
                    >
                </li>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin/users"
                        class="block hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                        >Users</router-link
                    >
                </li>
                <li>
                    <router-link
                        to="/dashboard-admin/settings"
                        class="block hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                        >Settings</router-link
                    >
                </li>
            </ul>
        </div>
    </aside>
</template>

<script>
export default {
    name: "Sidebar",
};
</script>
