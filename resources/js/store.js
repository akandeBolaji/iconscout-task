/*
    Imports Vue and Vuex
*/
import Vue from 'vue'
import Vuex from 'Vuex'

/*
    Initializes Vuex on Vue.
*/
Vue.use(Vuex)

import { icon } from './modules/icon.js'

/*
  Exports our data store.
*/
export default new Vuex.Store({
  modules: {
   icon
  }
});
