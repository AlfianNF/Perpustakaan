<template>
    <div class="flex">
        <Sidebar />
        <div class="ml-64 flex flex-col w-full">
            <Navbar />
            <div class="flex-1 p-4 max-h-screen">
                <h1 class="text-2xl font-semibold mb-4">Daftar Denda</h1>
                <div v-if="loading">Loading...</div>
                <div v-else>
                    <div class="bg-white shadow rounded-md p-4">
                        <table
                            class="min-w-full bg-white border border-gray-200 text-center"
                        >
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">
                                        No
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        User
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Buku
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Tanggal Kembali
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Denda
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(denda, index) in dendaList"
                                    :key="denda.id"
                                >
                                    <td class="border border-gray-300 p-2">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        {{ denda.peminjaman.user_pinjam.name }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        {{ denda.peminjaman.buku.title }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        {{ denda.tgl_kembali }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        Rp {{ denda.denda }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <button
                                            @click="bayar(denda.id)"
                                            class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-md mr-2"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-circle-dollar-sign-icon lucide-circle-dollar-sign mr-2"
                                            >
                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                />
                                                <path
                                                    d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"
                                                />
                                                <path d="M12 18V6" />
                                            </svg>
                                            Bayar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div
                v-if="modalVisible"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
            >
                <div class="bg-white p-6 rounded-md w-1/3">
                    <h2 class="text-lg font-semibold mb-4">Pembayaran Denda</h2>
                    <p>Masukkan nominal pembayaran untuk denda</p>
                    <input
                        v-model="paymentAmount"
                        type="number"
                        class="border rounded p-2 w-full mb-4"
                        placeholder="Nominal Pembayaran"
                    />
                    <div class="flex justify-end">
                        <button
                            @click="modalVisible = false"
                            class="px-3 py-1 bg-gray-300 rounded-md mr-2"
                        >
                            Batal
                        </button>
                        <button
                            @click="prosesPembayaran"
                            class="px-3 py-1 bg-blue-500 text-white rounded-md"
                        >
                            Proses
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "Denda",
    components: {
        Sidebar,
        Navbar,
    },
    data() {
        return {
            dendaList: [],
            loading: true,
            modalVisible: false,
            dendaIdToPay: null,
            paymentAmount: 0,
        };
    },
    mounted() {
        this.fetchDendaList();
    },
    methods: {
        async fetchDendaList() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");
                const response = await axios.get(
                    "http://belajar.test/api/kembali/list",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                console.log(response.data.data.data);
                this.dendaList = response.data.data.data.filter(
                    (item) => parseFloat(item.denda) > 0
                );
            } catch (err) {
                console.error("Error fetching denda list:", err);
            } finally {
                this.loading = false;
            }
        },
        bayar(dendaId) {
            this.dendaIdToPay = dendaId;
            this.paymentAmount = 0;
            this.modalVisible = true;
        },
        async prosesPembayaran() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");
                const response = await axios.post(
                    `http://belajar.test/api/kembali/${this.dendaIdToPay}/denda`,
                    {
                        jumlah_bayar: this.paymentAmount,
                    },
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                console.log("Pembayaran berhasil:", response.data);
                this.modalVisible = false;
                this.fetchDendaList();

                Swal.fire({
                    icon: "success",
                    title: "Pembayaran Berhasil!",
                    text: "Pembayaran denda telah berhasil diproses.",
                });
            } catch (err) {
                console.error("Error melakukan pembayaran:", err);
                Swal.fire({
                    icon: "error",
                    title: "Pembayaran Gagal!",
                    text: "Terjadi kesalahan saat memproses pembayaran.",
                });
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
