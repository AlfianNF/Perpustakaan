<template>
    <div class="laravel-select">
        <div class="laravel-select-input" @click="toggleDropdown">
            <input
                v-model="searchQuery"
                type="text"
                :placeholder="searchPlaceholder"
                class="laravel-select-input-field"
            />
            <div class="laravel-select-arrow">&#9662;</div>
        </div>
        <ul v-if="isDropdownOpen" class="laravel-select-dropdown">
            <li
                v-for="option in filteredOptions"
                :key="option[reduce(option)]"
                @click="selectOption(option)"
                class="laravel-select-option"
            >
                {{ option[label] }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        options: {
            type: Array,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
        reduce: {
            type: Function,
            required: true,
        },
        placeholder: {
            type: String,
            default: "Pilih",
        },
        searchPlaceholder: {
            type: String,
            default: "Cari...",
        },
        modelValue: {
            type: [String, Number, null],
            default: null,
        },
    },
    data() {
        return {
            searchQuery: "",
            isDropdownOpen: false,
            selectedOptionLabel: "", // Tambahkan properti ini
        };
    },
    computed: {
        selectedValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            },
        },
        filteredOptions() {
            if (!this.searchQuery) {
                return this.options;
            }
            const query = this.searchQuery.toLowerCase();
            return this.options.filter((option) => {
                const labelValue = String(option[this.label]).toLowerCase();
                return labelValue.includes(query);
            });
        },
    },
    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
        },
        selectOption(option) {
            this.$emit("update:modelValue", this.reduce(option));
            this.isDropdownOpen = false;
            this.selectedOptionLabel = option[this.label]; // Simpan label opsi yang dipilih
            this.searchQuery = option[this.label]; // Tampilkan label di input pencarian
        },
    },
    watch: {
        modelValue(newValue) {
            if (newValue === null) {
                this.searchQuery = "";
            } else {
                const selectedOption = this.options.find(
                    (option) => this.reduce(option) === newValue
                );
                if (selectedOption) {
                    this.searchQuery = selectedOption[this.label];
                }
            }
        },
    },
};
</script>

<style scoped>
.laravel-select {
    position: relative;
    width: 100%;
}

.laravel-select-input {
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px;
    cursor: pointer;
}

.laravel-select-input-field {
    flex-grow: 1;
    border: none;
    outline: none;
    padding: 0;
}

.laravel-select-arrow {
    margin-left: 8px;
}

.laravel-select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
    list-style: none;
    padding: 0;
    margin: 0;
}

.laravel-select-option {
    padding: 8px;
    cursor: pointer;
}

.laravel-select-option:hover {
    background-color: #f0f0f0;
}
</style>
