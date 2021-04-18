<template>
  <div>
    <div class="flex justify-center w-full p-4 m-2 align-center">
      <p class="text-2xl font-bold text-gray-700 uppercase">
        Home of <span class="text-blue-500">Icons</span> (Limited Version)
      </p>
    </div>
    <search-bar />
    <div v-if="!search">
      <div class="m-2 mt-20 text-2xl font-bold text-center">
        Enter Your Search Term
      </div>
    </div>
    <div v-else>
      <div v-if="searchResults.length > 0">
        <p class="m-10 font-extrabold text-gray-700">Search Results</p>
        <div class="flex flex-wrap m-6 justify-center w-full lg:justify-start">
          <icon-card
            v-for="icon in searchResults"
            :key="icon.id"
            :icon="icon"
          />
        </div>
      </div>
      <div class="m-10" v-else-if="searching">Searching....</div>
      <div v-else class="m-2 mt-20 text-2xl font-bold text-center">
        No Search Result
      </div>
    </div>
  </div>
</template>
<script>
import SearchBar from "./Searchbar";
import IconCard from "./IconCard";

export default {
  // created() {
  //     console.log(this.costs, this.topics, this.deliveries);
  // },
  computed: {
    search() {
      return this.$store.getters.getSearchQuery;
    },
    searchResults() {
      console.log("hi", this.$store.getters.getSearchResult);
      return this.$store.getters.getSearchResult;
    },
    searching() {
      return this.$store.getters.getSearchStatus;
    },
  },
  components: {
    IconCard,
    SearchBar,
  },
};
</script>
