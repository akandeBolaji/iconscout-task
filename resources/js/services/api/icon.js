export default {
    search (term) {
        return axios.get('/api/v1/search-no-jwt', {
            params: {
                query: term.query,
                price: term.cost ? term.cost : null,
                style: term.style ? term.style : null
          }
      })
    },
  }
