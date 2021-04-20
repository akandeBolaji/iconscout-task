<template>
  <div class="flex flex-col">
    <div class="w-full flex justify-center m-4 align-center">
      <input
        type="search"
        autofocus="autofocus"
        class="w-full max-w-md p-3 m-3 text-blue-700 placeholder-blue-700 transition bg-black-200 border-blue-500 rounded shadow outline-none focus:border-yellow-500 focus:shadow-outline border-1"
        placeholder="Search..."
        v-model="query"
        @input="searchIcon"
      />
    </div>

    <filter-bar @changeFilter="changeFilter" />
  </div>
</template>
<script>
import FilterBar from "./FilterBar";
export default {
  data() {
    return {
      query: null,
      cost: null,
      style: null,
      debounce: null,
    };
  },
  computed: {
    // search() {
    //     return this.$store.getters.getSearchTerm
    // }
  },
  methods: {
    changeFilter(data) {
      console.log(data);
      this.style = data.style;
      this.cost = data.cost;
      this.color = data.color;
      this.color_type = data.color_type;
      this.searchIcon();
    },
    searchIcon() {
      if (!this.query) {
        return;
      }

      let req = {
        query: this.query,
        cost: this.cost,
        style: this.style,
        color: this.color,
        color_type: this.color_type,
      };
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.$store.dispatch("searchIcon", req);
        console.log(req);
      }, 600);
    },
  },
  components: {
    FilterBar,
  },
};
</script>
