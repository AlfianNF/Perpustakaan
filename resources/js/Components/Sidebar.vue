<template>
    <aside class="bg-gray-800 text-white w-64 min-h-screen">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Admin Panel</h2>
            <ul>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin"
                        class="flex items-center hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 lucide lucide-house-icon lucide-house"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"
                            />
                            <path
                                d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                            />
                        </svg>
                        Dashboard
                    </router-link>
                </li>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin/users"
                        class="flex items-center hover:bg-gray-700 p-2 rounded"
                        :class="{ 'bg-gray-700': isUsersActive }"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 lucide lucide-user-icon lucide-user"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"
                            />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Users
                    </router-link>
                </li>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin/pinjam"
                        class="flex items-center hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 lucide lucide-book-plus-icon lucide-book-plus"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 7v6" />
                            <path
                                d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"
                            />
                            <path d="M9 10h6" />
                        </svg>
                        Pinjam
                    </router-link>
                </li>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin/kembali"
                        class="flex items-center hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 lucide lucide-book-check-icon lucide-book-check"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"
                            />
                            <path d="m9 9.5 2 2 4-4" />
                        </svg>
                        Kembali
                    </router-link>
                </li>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin/denda"
                        class="flex items-center hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 lucide lucide-circle-dollar-sign-icon lucide-circle-dollar-sign"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <path
                                d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"
                            />
                            <path d="M12 18V6" />
                        </svg>
                        Denda
                    </router-link>
                </li>
                <li class="mb-2">
                    <router-link
                        to="/dashboard-admin/settings"
                        class="flex items-center hover:bg-gray-700 p-2 rounded"
                        active-class="bg-gray-700"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-2 lucide lucide-settings-icon lucide-settings"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"
                            />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        Settings
                    </router-link>
                </li>
            </ul>
        </div>
    </aside>
</template>

<script>
export default {
    name: "Sidebar",
    computed: {
        isUsersActive() {
            return this.$route.path.startsWith("/dashboard-admin/users");
        },
    },
};
</script>
