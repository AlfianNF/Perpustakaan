<template>
    <div class="flex">
        <Sidebar />
        <div class="flex-1 flex flex-col">
            <Navbar />
            <div class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                <h1 class="text-xl font-semibold mb-4">Daftar Peminjaman</h1>

                <div class="mb-4 flex gap-4 items-center">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari buku atau user..."
                        class="p-2 border rounded w-1/3"
                        @keyup.enter="fetchPinjam"
                    />

                    <button
                        @click="fetchPinjam"
                        class="px-4 py-2 bg-blue-500 text-white rounded inline-flex items-center gap-2"
                    >
                        Cari
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </button>

                    <input
                        v-model="filterDate"
                        type="date"
                        class="p-2 border rounded"
                        @change="fetchPinjam"
                    />

                    <button
                        @click="toggleFilter"
                        class="px-4 py-2 bg-gray-200 rounded inline-flex items-center gap-2"
                    >
                        Filter
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                d="M10 20a1 1 0 0 0 .555.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.74 1.67l7.223 7.989A2 2 0 0 1 10 14v6Z"
                            ></path>
                        </svg>
                    </button>

                    <div
                        v-if="showFilterOptions"
                        class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center"
                    >
                        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                            <h2
                                class="text-xl font-semibold text-gray-800 mb-4 text-center"
                            >
                                Pilih Status Peminjaman
                            </h2>
                            <div class="space-y-3">
                                <label
                                    class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-100 cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        v-model="filterStatus"
                                        value="all"
                                        @change="fetchPinjam"
                                        class="h-5 w-5 accent-blue-500"
                                    />
                                    <span class="text-gray-700 text-lg"
                                        >Semua</span
                                    >
                                </label>
                                <label
                                    class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-100 cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        v-model="filterStatus"
                                        value="dipinjam"
                                        @change="fetchPinjam"
                                        class="h-5 w-5 accent-blue-500"
                                    />
                                    <span class="text-gray-700 text-lg"
                                        >Dipinjam</span
                                    >
                                </label>
                                <label
                                    class="flex items-center gap-2 p-3 border rounded-lg hover:bg-gray-100 cursor-pointer"
                                >
                                    <input
                                        type="radio"
                                        v-model="filterStatus"
                                        value="dikembalikan"
                                        @change="fetchPinjam"
                                        class="h-5 w-5 accent-blue-500"
                                    />
                                    <span class="text-gray-700 text-lg"
                                        >Dikembalikan</span
                                    >
                                </label>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button
                                    @click="toggleFilter"
                                    class="px-5 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition"
                                >
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="showModal = true"
                        class="px-4 py-2 bg-green-500 text-white rounded inline-flex items-center gap-2"
                    >
                        Tambah
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path d="M5 12h14M12 5v14"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-white shadow rounded-md p-4">
                    <table class="w-full border text-center">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">User</th>
                                <th class="border px-4 py-2">Buku</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Tanggal Pinjam</th>
                                <th class="border px-4 py-2">
                                    Tanggal Kembali
                                </th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(pinjam, index) in daftarPinjam"
                                :key="pinjam.id"
                            >
                                <td class="border px-4 py-2">
                                    {{
                                        (currentPage - 1) *
                                            daftarPinjam.length +
                                        index +
                                        1
                                    }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ pinjam.user_pinjam.name }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ pinjam.buku.title }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ pinjam.status }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ formatTanggal(pinjam.tgl_pinjam) }}
                                </td>
                                <td class="border px-4 py-2">
                                    {{ formatTanggal(pinjam.tgl_kembali) }}
                                </td>
                                <td class="border px-4 py-2">
                                    <button
                                        @click="editPinjam(pinjam.id)"
                                        class="px-3 py-1 bg-blue-500 text-white rounded-md mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deletePinjam(pinjam.id)"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex justify-between items-center mt-4">
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1"
                            class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <span
                            >Halaman {{ currentPage }} dari
                            {{ totalPages }}</span
                        >
                        <button
                            @click="nextPage"
                            :disabled="currentPage >= totalPages"
                            class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center"
        >
            <div class="bg-white p-6 rounded-md w-1/2">
                <h2 class="text-2xl font-semibold mb-4">
                    Tambah Peminjaman Baru
                </h2>
                <form @submit.prevent="submitPinjam">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700"
                            >User</label
                        >
                        <LaravelSelect
                            v-model="newPinjam.id_user"
                            :options="users"
                            label="name"
                            :reduce="(user) => user.id"
                            placeholder="Pilih User"
                            :laravelSelectHtml="userSelectHtml"
                            class="mt-1 w-full"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700"
                            >Buku</label
                        >
                        <LaravelSelect
                            v-model="newPinjam.id_buku"
                            :options="books"
                            label="title"
                            :reduce="(book) => book.id"
                            placeholder="Pilih Buku"
                            :laravelSelectHtml="bookSelectHtml"
                            class="mt-1 w-full"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Tanggal Pinjam
                        </label>
                        <input
                            v-model="newPinjam.tgl_pinjam"
                            type="date"
                            class="mt-1 p-2 border rounded-md w-full"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Tanggal Kembali
                        </label>
                        <input
                            v-model="newPinjam.tgl_kembali"
                            type="date"
                            class="mt-1 p-2 border rounded-md w-full"
                        />
                    </div>
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 bg-gray-300 rounded-md"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">
                Edit Peminjaman
            </h2>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1"
                    >Tanggal Pinjam</label
                >
                <input
                    type="date"
                    v-model="editedPinjam.tgl_pinjam"
                    class="w-full p-2 border rounded"
                />
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1"
                    >Tanggal Kembali</label
                >
                <input
                    type="date"
                    v-model="editedPinjam.tgl_kembali"
                    class="w-full p-2 border rounded"
                />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    @click="showEditModal = false"
                    class="px-5 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
                >
                    Batal
                </button>
                <button
                    @click="updatePinjam"
                    class="px-5 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import LaravelSelect from "./../LaravelSelect.vue";
import "vue-select/dist/vue-select.css";
import axios from "axios";

export default {
    name: "Pinjam",
    components: {
        Sidebar,
        Navbar,
        LaravelSelect,
    },
    data() {
        return {
            daftarPinjam: [],
            users: [],
            books: [],
            userSelectHtml: "",
            bookSelectHtml: "",
            searchQuery: "",
            filterDate: "",
            showFilterOptions: false,
            currentPage: 1,
            totalPages: 1,
            baseUrl: "http://belajar.test/",
            showModal: false,
            newPinjam: {
                id_user: null,
                id_buku: null,
                tgl_pinjam: null,
                tgl_kembali: null,
            },
            showEditModal: false,
            editedPinjam: {
                id: null,
                tgl_pinjam: "",
                tgl_kembali: "",
            },
        };
    },
    mounted() {
        this.fetchPinjam();
        this.fetchUsers();
        this.fetchBooks();
    },
    methods: {
        async fetchPinjam() {
            try {
                const response = await axios.get(
                    `${this.baseUrl}api/pinjam/list`,
                    {
                        params: {
                            page: this.currentPage,
                            search: this.searchQuery || undefined,
                            tgl_pinjam: this.filterDate || undefined,
                            status:
                                this.filterStatus !== "all"
                                    ? this.filterStatus
                                    : undefined,
                        },
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                this.daftarPinjam = response.data.data.data;
                this.totalPages = response.data.last_page || 1;
            } catch (error) {
                console.error("Gagal mengambil data pinjam:", error);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.fetchPinjam();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.fetchPinjam();
            }
        },
        async deletePinjam(id) {
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                try {
                    await axios.delete(
                        `${this.baseUrl}api/pinjam/${id}/delete`,
                        {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                    "token"
                                )}`,
                            },
                        }
                    );
                    this.fetchPinjam();
                } catch (error) {
                    console.error("Gagal menghapus pinjam:", error);
                }
            }
        },
        formatTanggal(tanggal) {
            if (!tanggal) return "";
            const options = { day: "numeric", month: "long", year: "numeric" };
            return new Date(tanggal).toLocaleDateString("id-ID", options);
        },
        async submitPinjam() {
            try {
                await axios.post(
                    `${this.baseUrl}api/pinjam/create`,
                    this.newPinjam,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                this.showModal = false;
                this.newPinjam = {
                    id_user: null,
                    id_buku: null,
                    tgl_pinjam: null,
                    tgl_kembali: null,
                };
                this.fetchPinjam();
            } catch (error) {
                console.error("Gagal menambahkan pinjam:", error);
            }
        },
        fetchAddPinjam() {
            this.showModal = true;
        },
        async fetchUsers() {
            try {
                const response = await axios.get(
                    `${this.baseUrl}api/user/list`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                this.users = response.data.data.data;
            } catch (error) {
                console.error("Gagal mengambil data pengguna:", error);
            }
        },
        async fetchBooks() {
            try {
                const response = await axios.get(
                    `${this.baseUrl}api/buku/list`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                this.books = response.data.data.data;
            } catch (error) {
                console.error("Gagal mengambil data buku:", error);
            }
        },
        toggleFilter() {
            this.showFilterOptions = !this.showFilterOptions;
        },
        async editPinjam(id) {
            try {
                const token = localStorage.getItem("token"); 
                const response = await axios.get(`/api/pinjam/${id}/show`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                this.editedPinjam = response.data;
                this.showEditModal = true;
            } catch (error) {
                console.error("Error fetching data:", error);
            }
        },

        async updatePinjam() {
    try {
        const token = localStorage.getItem("token");
        await axios.put(`/api/pinjam/${this.editedPinjam.id}/update`, {
            tgl_pinjam: this.editedPinjam.tgl_pinjam,
            tgl_kembali: this.editedPinjam.tgl_kembali
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        alert("Data berhasil diperbarui!");
        this.showEditModal = false;

        window.location.reload(); 
    } catch (error) {
        console.error("Error updating data:", error);
    }
}

    },
};
</script>
