// src/boot/axios.js
import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { LocalStorage } from 'quasar'

const api = axios.create({
  baseURL: 'http://localhost:8000/backend/api/',
})

export default boot(({ app }) => {
  api.accessToken = LocalStorage.getItem('accessToken')
  api.user = LocalStorage.getItem('user')
  api.userTypes = LocalStorage.getItem('userTypes')
  api.courseStatusTypes = LocalStorage.getItem('courseStatusTypes')

  // Make axios available globally
  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$api = api
})

export { api }
