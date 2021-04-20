/*
|-------------------------------------------------------------------------------
| VUEX modules/auth.js
|-------------------------------------------------------------------------------
| The Vuex data store for   Auth
*/

import iconAPI from "../services/api/icon.js";

export const icon = {
    /*
    Defines the state being monitored for the module.
  */
    state: {
        searchTerm: {
            query: "",
            cost: "",
            style: ""
        },
        searchResult: [],
        searchStatus: false
    },

    /*
Defines the actions used to retrieve the data.
*/
    actions: {
        searchIcon({ commit, state }, term) {
            commit("setSearchTerm", term);
            commit("setSearchStatus", true);
            if (state.searchTerm) {
                iconAPI
                    .search(term)
                    .then(res => {
                        commit("setSearchStatus", false);
                        console.log(res.data.response.items.data);
                        commit("setSearchResult", res.data.response.items.data);
                    })
                    .catch(error => {
                        console.log("err", error);
                        commit("setSearchStatus", false);
                        commit("setSearchResult", []);
                        // commit('setAuthState', false);
                    });
            }
        }
    },

    /*
Defines the mutations used
*/
    mutations: {
        setSearchTerm(state, term) {
            state.searchTerm = term;
        },
        setSearchResult(state, term) {
            state.searchResult = term;
        },
        setSearchStatus(state, status) {
            state.searchStatus = status;
        }
    },

    /*
  Defines the getters used by the module
*/
    getters: {
        /*
        Returns the posts load status.
    */
        getSearchQuery(state) {
            return state.searchTerm.query;
        },
        getSearchTerm(state) {
            return state.searchTerm;
        },
        getSearchResult(state) {
            return state.searchResult;
        },
        getSearchStatus(state) {
            return state.searchStatus;
        }
    }
};
