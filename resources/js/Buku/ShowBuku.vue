<template>
    <div class="min-h-screen bg-gray-100 p-8 flex flex-col items-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            Detail Buku
        </h1>

        <div v-if="loading" class="text-center text-gray-600">Loading...</div>
        <div v-if="error" class="text-red-500 text-center">{{ error }}</div>

        <div
            v-if="!loading && book"
            class="bg-white p-6 rounded-lg shadow-md w-full max-w-xl"
        >
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">
                {{ book.title }}
            </h2>

            <img
                :src="getImageUrl(book.image)"
                alt="Buku"
                class="w-64 h-96 object-cover rounded-lg mx-auto mb-4 text-center"
            />

            <div class="text-gray-700 space-y-2">
                <p><strong>ISBN:</strong> {{ book.isbn }}</p>
                <p><strong>Penulis:</strong> {{ book.author }}</p>
                <p>
                    <strong>Kategori:</strong>
                    {{ book.category?.name || "Tidak ada kategori" }}
                </p>
                <p><strong>Tanggal Terbit:</strong> {{ book.publish_date }}</p>
                <p>
                    <strong>Deskripsi:</strong> Lorem ipsum dolor sit amet
                    consectetur adipisicing elit. Maxime voluptates in officia
                    reprehenderit autem ducimus, ut, exercitationem, adipisci
                    aliquid quia debitis aperiam possimus recusandae. Assumenda
                    et iure rerum iste amet. Consequuntur, unde distinctio!
                    Adipisci sed aut placeat quas, laborum necessitatibus
                    voluptas voluptatibus officia nulla veniam, non ipsa quos.
                    Error repudiandae laudantium ullam totam consequuntur
                    veritatis magni sed harum asperiores iusto. Aut nisi
                    praesentium tenetur quis provident consequatur numquam. Eius
                    aspernatur expedita beatae. Autem repellendus in maiores
                    soluta minima ab, eius voluptatum eum commodi eligendi,
                    aperiam harum blanditiis unde doloremque sunt. Optio, nihil?
                    Praesentium quod eveniet, hic explicabo animi modi. Quam
                    doloribus, amet perferendis magnam voluptatum voluptates
                    laudantium dolores! Nisi reprehenderit, praesentium eum
                    debitis iusto possimus ipsum labore aperiam non voluptas?
                    Eos aspernatur necessitatibus libero suscipit nisi
                    doloremque exercitationem tempora temporibus eius
                    architecto, iste rem est sit voluptatem placeat distinctio
                    facere, amet quidem? Velit, esse architecto dolorum quo
                    officiis enim vero? Dignissimos dolores molestiae neque
                    voluptatem eius maxime quas quidem doloribus quod ut eaque
                    soluta ullam itaque sapiente harum, cupiditate delectus
                    repellat aspernatur facilis iusto praesentium atque
                    voluptatibus et iure! Facere. Eveniet quisquam aperiam sit,
                    repudiandae eius dolores architecto voluptatum quo aliquid
                    earum repellat enim vero sapiente natus mollitia quasi quia
                    in incidunt, omnis id error culpa et pariatur doloremque?
                    Magnam. Eius, voluptate? Vitae mollitia iusto provident hic
                    commodi ipsa sequi, ut praesentium rem magni sit ullam et
                    facilis, asperiores dolor. Ipsum quo veritatis odio
                    exercitationem iusto neque necessitatibus numquam ex.
                    Molestias debitis quidem eius voluptatibus nihil doloribus
                    deleniti magni officia, ut nemo nisi, ullam qui temporibus
                    incidunt earum! Reiciendis dolor exercitationem fugit ut
                    quasi similique ab quas repudiandae? Molestiae, eaque!
                    Blanditiis corporis fugit ut laborum minus quibusdam dolor
                    consectetur quaerat velit, obcaecati ratione amet
                    repellendus nostrum quam necessitatibus nam unde odit
                    corrupti veniam. Ipsa maxime ex vel. Accusamus, nulla
                    delectus!
                </p>
            </div>
            <button
                @click="$router.push('/dashboard')"
                class="mt-6 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
                Kembali
            </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "BookShow",
    data() {
        return {
            book: null,
            loading: true,
            error: null,
            baseURL: "http://belajar.test/",
        };
    },
    async created() {
        await this.fetchBook();
    },
    methods: {
        async fetchBook() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");
                const bookId = this.$route.params.id;
                const response = await axios.get(
                    `${this.baseURL}api/buku/${bookId}/show`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                this.book = response.data;
                console.log("Book fetched:", this.book);
            } catch (err) {
                console.error(
                    "Error fetching book:",
                    err.response ? err.response.data : err
                );
                this.error = "Terjadi kesalahan saat mengambil data buku.";
            } finally {
                this.loading = false;
            }
        },
        getImageUrl(imagePath) {
            if (!imagePath) return "https://picsum.photos/300/450";
            if (imagePath.startsWith("http")) return imagePath;
            return `${this.baseURL}storage/${imagePath}`;
        },
    },
};
</script>
