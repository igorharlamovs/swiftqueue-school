import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { LocalStorage, Loading } from 'quasar'

export const useInitStore = defineStore('init', {
  state: () => ({
    userTypes: LocalStorage.getItem('userTypes') || [],
    courseStatusTypes: LocalStorage.getItem('courseStatusTypes') || [],
  }),

  getters: {},

  actions: {
    async initialiseCommonLookups() {
      try {
        Loading.show({ message: 'Initialising...' })

        const response = await api.get('/commonlookups')

        this.userTypes = response.data.userTypes
        this.courseStatusTypes = response.data.courseStatusTypes

        LocalStorage.set('userTypes', this.userTypes)
        LocalStorage.set('courseStatusTypes', this.courseStatusTypes)

        Loading.hide()
      } catch (error) {
        Loading.hide()
        console.log(error)
      }
    },
  },
})
