<template>
    <div>
        <div
            class="flex justify-between items-center px-8 py-4 bg-white shadow-md mb-6 rounded-b-lg"
        >
            <h1 class="text-2xl font-bold text-gray-800">Profil Pengguna</h1>
            <div class="flex items-center space-x-4">
                <button
                    @click="logout"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600"
                >
                    Logout
                </button>
            </div>
        </div>

        <div class="flex justify-center">
            <div class="w-full max-w-6xl p-6">
                <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        Profil Pengguna
                    </h2>
                    <div
                        class="flex flex-col md:flex-row items-start md:items-center space-y-6 md:space-y-0 md:space-x-8"
                    >
                        <img
                            :src="
                                profile.image ||
                                'https://images.pexels.com/photos/1704488/pexels-photo-1704488.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500'
                            "
                            alt="Profile Image"
                            class="w-32 h-32 object-cover rounded-lg border"
                        />

                        <div
                            class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div>
                                <label class="block text-gray-700 font-medium"
                                    >Nama</label
                                >
                                <input
                                    type="text"
                                    v-model="profile.name"
                                    class="w-full mt-1 p-2 border rounded"
                                    disabled
                                />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium"
                                    >No Induk</label
                                >
                                <input
                                    type="text"
                                    v-model="profile.no_induk"
                                    class="w-full mt-1 p-2 border rounded"
                                    disabled
                                />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 font-medium"
                                    >Email</label
                                >
                                <input
                                    type="email"
                                    v-model="profile.email"
                                    class="w-full mt-1 p-2 border rounded"
                                    disabled
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md mb-8">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">
                        Buku yang Dipinjam
                    </h2>
                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4"
                    >
                        <div
                            v-for="book in pinjamBooks"
                            :key="book.id"
                            class="bg-gray-50 p-3 rounded-lg shadow hover:shadow-md transition text-center"
                        >
                            <img
                                :src="book.image_full"
                                :alt="book.buku?.title"
                                class="w-32 h-32 object-contain rounded mb-2 mx-auto bg-white"
                            />
                            <h3
                                class="text-sm font-semibold text-gray-800 truncate"
                            >
                                {{ book.buku?.title || "-" }}
                            </h3>
                            <p class="text-xs text-gray-600">
                                Penulis: {{ book.buku?.author || "-" }}
                            </p>
                            <p class="text-xs text-gray-600">
                                Pinjam: {{ book.tgl_pinjam_formatted }}
                            </p>
                            <p class="text-xs text-gray-600">
                                Kembali: {{ book.tgl_kembali_formatted }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">
                        Buku yang Pernah Dibaca
                    </h2>
                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4"
                    >
                        <div
                            v-for="book in recentlyRead"
                            :key="book.id"
                            class="bg-gray-50 p-3 rounded-lg shadow hover:shadow-md transition text-center"
                        >
                            <img
                                :src="book.image_full"
                                :alt="book.buku?.title"
                                class="w-32 h-32 object-contain rounded mb-2 mx-auto bg-white"
                            />
                            <h3
                                class="text-sm font-semibold text-gray-800 truncate"
                            >
                                {{ book.buku?.title || "-" }}
                            </h3>
                            <p class="text-xs text-gray-600">
                                Penulis: {{ book.buku?.author || "-" }}
                            </p>
                            <p class="text-xs text-gray-600">
                                Pinjam: {{ book.tgl_pinjam_formatted }}
                            </p>
                            <p class="text-xs text-gray-600">
                                Kembali: {{ book.tgl_kembali_formatted }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex mb-5 ml-[10%]">
            <button
                @click="$router.push('/dashboard')"
                class="mt-8 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
                Kembali
            </button>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import axios from "axios";

export default {
    name: "Profile",
    components: {
        Sidebar,
        Navbar,
    },
    data() {
        return {
            profile: {
                id: null,
                name: "",
                no_induk: "",
                email: "",
                image: null,
            },
            imageFile: null,
            baseURL: "http://belajar.test/",
            recentlyRead: [],
            pinjamBooks: [],
        };
    },
    mounted() {
        this.fetchProfile();
        this.fetchRecentlyRead();
        this.fetchPinjamBooks();
    },
    methods: {
        logout() {
            localStorage.removeItem("token");
            window.location.href = "/";
        },
        async fetchProfile() {
            try {
                const response = await axios.get(
                    "http://belajar.test/api/user",
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                this.profile = response.data;
                if (
                    this.profile.image &&
                    !this.profile.image.startsWith("http")
                ) {
                    this.profile.image =
                        this.baseURL + "storage/" + this.profile.image;
                }
            } catch (error) {
                console.error("Error fetching profile:", error);
            }
        },

        async fetchRecentlyRead() {
            try {
                const response = await axios.get(
                    "http://belajar.test/api/buku/recently-read",
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                this.recentlyRead = response.data.map((book) => {
                    const options = {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    };

                    if (book.tgl_pinjam) {
                        const pinjamDate = new Date(book.tgl_pinjam);
                        book.tgl_pinjam_formatted =
                            pinjamDate.toLocaleDateString("id-ID", options);
                    }

                    if (book.tgl_kembali) {
                        const kembaliDate = new Date(book.tgl_kembali);
                        book.tgl_kembali_formatted =
                            kembaliDate.toLocaleDateString("id-ID", options);
                    }

                    if (book.buku && book.buku.image) {
                        book.image_full =
                            this.baseURL + "storage/" + book.buku.image;
                    } else {
                        book.image_full = "https://via.placeholder.com/150";
                    }

                    return book;
                });
            } catch (error) {
                console.error("Error fetching recently read books:", error);
            }
        },

        async fetchPinjamBooks() {
            try {
                const response = await axios.get(
                    "http://belajar.test/api/profil/pinjam",
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                this.pinjamBooks = response.data.map((book) => {
                    const options = {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    };

                    if (book.tgl_pinjam) {
                        const pinjamDate = new Date(book.tgl_pinjam);
                        book.tgl_pinjam_formatted =
                            pinjamDate.toLocaleDateString("id-ID", options);
                    }

                    if (book.tgl_kembali) {
                        const kembaliDate = new Date(book.tgl_kembali);
                        book.tgl_kembali_formatted =
                            kembaliDate.toLocaleDateString("id-ID", options);
                    }

                    if (book.buku && book.buku.image) {
                        book.image_full =
                            this.baseURL + "storage/" + book.buku.image;
                    } else {
                        book.image_full = "https://via.placeholder.com/150";
                    }
                    return book;
                });
            } catch (error) {
                console.error("Error fetching pinjam books:", error);
            }
        },
    },
};
</script>
