<template>
  <div
    class="flex-none w-64 max-w-sm m-2 bg-white border rounded shadow-lg hover:opacity-50"
  >
    <img
      v-if="icon.img_url"
      class="object-cover w-full h-64"
      :src="icon.img_url"
      alt="None"
    />
    <div
      v-else
      class="flex justify-center h-64 m-2 text-xl text-white truncate bg-gray-500 border rounded-full"
    >
      <span class="w-full text-center mt-28">{{ icon.name }}</span>
    </div>
    <div class="px-2 pt-4">
      <div class="mb-2 text-xl font-bold text-orange-500 truncate">
        {{ icon.name }}
      </div>
      <p class="text-base text-gray-700 truncate">
        Price - {{ icon.price == 0 ? "Free" : `$${icon.price}` }}
      </p>
      <p class="text-base text-gray-700 truncate">Style - {{ icon.style }}</p>
      <p class="text-base text-gray-700 truncate">
        Tags -
        <span v-for="tag in icon.tags" :key="tag.id">{{ tag.value }},</span>
      </p>
      <p class="text-base mb-4 text-gray-700 truncate">
        Categories -
        <span v-for="category in icon.categories" :key="category.id"
          >{{ category.value }},</span
        >
      </p>
    </div>
  </div>
</template>
<script>
export default {
  mounted() {},
  props: {
    icon: Object,
  },
};
</script>
