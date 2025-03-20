<template>
    <nav class="bg-gray-900 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-lg font-semibold">Admin Dashboard</h1>
            <div class="relative">
                <button @click="toggleDropdown" class="flex items-center">
                    <img
                        :src="
                            profileImageUrl ||
                            'https://images.pexels.com/photos/1704488/pexels-photo-1704488.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500'
                        "
                        alt="Profile"
                        class="w-8 h-8 rounded-full mr-2"
                    />
                    <span>{{ userName }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>
                <div
                    v-if="isDropdownOpen"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg overflow-hidden z-10"
                >
                    <a
                        href="/dashboard-admin/settings"
                        class="flex items-center px-4 py-2 text-sm text-gray-800 hover:bg-gray-100"
                    >
                        <img
                            src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLXNldHRpbmdzIj48cGF0aCBkPSJNMTIuMjIgMmgtLjQ0YTIgMiAwIDAgMC0yIDJ2LjE4YTIgMiAwIDAgMS0xIDEuNzNsLS40My4yNWEyIDIgMCAwIDEtMiAwbC0uMTUtLjA4YTIgMiAwIDAgMC0yLjczLjczbC0uMjIuMzhhMiAyIDAgMCAwIC43MyAyLjczbC4xNS4xYTIgMiAwIDAgMSAxIDEuNzJ2LjUxYTIgMiAwIDAgMS0xIDEuNzRsLS4xNS4wOWEyIDIgMCAwIDAtLjczIDIuNzNsLjIyLjM4YTIgMiAwIDAgMCAyLjczLjczbC4xNS0uMDhhMiAyIDAgMCAxIDIgMGwuNDMuMjVhMiAyIDAgMCAxIDEgMS43M1YyMGEyIDIgMCAwIDAgMiAyaC40NGEyIDIgMCAwIDAgMi0ydi0uMThhMiAyIDAgMCAxIDEtMS43M2wuNDMtLjI1YTIgMiAwIDAgMSAyIDBsLjE1LjA4YTIgMiAwIDAgMCAyLjczLS43M2wuMjItLjM5YTIgMiAwIDAgMC0uNzMtMi43M2wtLjE1LS4wOGEyIDIgMCAwIDEtMS0xLjc0di0uNWEyIDIgMCAwIDEgMS0xLjc0bC4xNS0uMDlhMiAyIDAgMCAwIC43My0yLjczbC0uMjItLjM4YTIgMiAwIDAgMC0yLjczLS43M2wtLjE1LjA4YTIgMiAwIDAgMS0yIDBsLS40My0uMjVhMiAyIDAgMCAxLTEtMS43M1Y0YTIgMiAwIDAgMC0yLTJ6Ii8+PGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMyIvPjwvc3ZnPg=="
                            alt="Settings"
                            class="w-5 h-5 mr-2"
                        />
                        Settings
                    </a>

                    <button
                        @click="logout"
                        class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-800 hover:bg-gray-100"
                    >
                        <img
                            src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWxvZy1vdXQiPjxwYXRoIGQ9Ik05IDIxSDVhMiAyIDAgMCAxLTItMlY1YTIgMiAwIDAgMSAyLTJoNCIvPjxwb2x5bGluZSBwb2ludHM9IjE2IDE3IDIxIDEyIDE2IDciLz48bGluZSB4MT0iMjEiIHgyPSI5IiB5MT0iMTIiIHkyPSIxMiIvPjwvc3ZnPg=="
                            alt="Logout"
                            class="w-5 h-5 mr-2"
                        />
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import axios from "axios";

export default {
    name: "Navbar",
    data() {
        return {
            isDropdownOpen: false,
            profileImageUrl: "",
            userName: "",
        };
    },
    mounted() {
        this.fetchUserProfile();
    },
    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
        },
        logout() {
            axios
                .post("/api/logout")
                .then(() => {
                    this.$router.push("/login");
                })
                .catch((error) => {
                    console.error("Logout failed:", error);
                });
        },
        async fetchUserProfile() {
            try {
                const response = await axios.get("/api/user", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                });

                if (response.data && response.data.id) {
                    const userResponse = await axios.get(
                        `/api/user/${response.data.id}/show`,
                        {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                    "token"
                                )}`,
                            },
                        }
                    );
                    if (userResponse.data || userResponse.data.image) {
                        this.profileImageUrl = userResponse.data.image;
                        this.userName = userResponse.data.name;
                    } else {
                        console.error(
                            "Profile image URL not found in API response."
                        );
                    }
                } else {
                    console.error("User ID not found in API response.");
                }
            } catch (error) {
                console.error("Error fetching user profile:", error);
            }
        },
    },
};
</script>
