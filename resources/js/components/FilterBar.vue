<template>
  <div class="flex flex-row justify-center w-full mb-1 align-center sm:mb-0">
    <div class="relative">
      <select
        @change="changeFilter"
        v-model="filter.style"
        class="block w-full h-full px-4 py-2 pr-8 leading-tight text-gray-700 bg-white border border-gray-400 appearance-none focus:outline-none focus:bg-white focus:border-gray-500"
      >
        <option value="">Style</option>
        <option value="Flat">Flat</option>
        <option value="Line">Line</option>
        <option value="DualTone">DualTone</option>
        <option value="Colored Outline">Colored Outline</option>
      </select>
      <div
        class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 pointer-events-none"
      >
        <svg
          class="w-4 h-4 fill-current"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
        >
          <path
            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
          />
        </svg>
      </div>
    </div>
    <div class="relative">
      <select
        @change="changeFilter"
        v-model="filter.cost"
        class="block w-full h-full px-4 py-2 pr-8 leading-tight text-gray-700 bg-white border border-gray-400 rounded-r appearance-none focus:outline-none focus:bg-white focus:border-gray-500"
      >
        <option value="">Cost ($)</option>
        <option value="free">Free</option>
        <option value="premium">Premium</option>
      </select>
      <div
        class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 pointer-events-none"
      >
        <svg
          class="w-4 h-4 fill-current"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
        >
          <path
            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
          />
        </svg>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      filter: {
        cost: "",
        style: "",
      },
    };
  },
  methods: {
    changeFilter() {
      this.$emit("changeFilter", this.filter);
    },
  },
};
</script>
