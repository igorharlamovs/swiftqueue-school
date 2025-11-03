import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { LocalStorage, Loading } from 'quasar'

export const useInitStore = defineStore('init', {
  state: () => ({
    userTypes: LocalStorage.getItem('userTypes') || [],
    courseStatusTypes: LocalStorage.getItem('courseStatusTypes') || [],
  }),

  getters: {
    /**
     * @param {Int} statusTypeId 
     * 
     * @returns {object}
     */
    userTypeById: (state) => (userTypeId) => {
      return state.userTypes.find(
        (userType) => userType.id === userTypeId
      )
    },

    /**
     * @param {Int} statusTypeId 
     * 
     * @returns {object}
     */
    courseStatusTypeById: (state) => (statusTypeId) => {
      return state.courseStatusTypes.find(
        (courseStatusType) => courseStatusType.id === statusTypeId
      )
    },
  },

  actions: {
    async initialiseCommonLookups() {
      try {
        Loading.show({ message: 'Initialising...' })

        const response = await api.get('/commonlookups')

        this.userTypes = response.data.data.userTypes
        this.courseStatusTypes = response.data.data.courseStatusTypes

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
