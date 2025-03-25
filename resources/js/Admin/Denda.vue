<template>
    <div class="flex">
        <Sidebar />
        <div class="flex-1 flex flex-col">
            <Navbar />
            <div class="flex-1 overflow-x-hidden overflow-y-auto p-4">
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
                                            <img
                                                src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNpcmNsZS1kb2xsYXItc2lnbiI+PGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTAiLz48cGF0aCBkPSJNMTYgOGgtNmEyIDIgMCAxIDAgMCA0aDRhMiAyIDAgMSAxIDAgNEg4Ii8+PHBhdGggZD0iTTEyIDE4VjYiLz48L3N2Zz4="
                                                alt=""
                                                class="mr-1"
                                            />
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
