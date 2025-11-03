<template>
  <q-card class="my-card bg-dark" :style="$q.screen.lt.md ? 'height: 100%' : 'width: 700px; max-height: 90vh;'">
    <!-- Header bar with dynamic title (Edit/Create) and close button -->
    <q-bar class="bg-teal text-white q-mb-md">
      <div class="text-h6">{{ isEdit ? 'Edit Course' : 'Create New Course' }}</div>
      <q-space />
      <q-btn flat dense round icon="close" v-close-popup>
        <q-tooltip class="bg-red">Close</q-tooltip>
      </q-btn>
    </q-bar>

    <!-- Course form -->
    <q-form @submit.prevent="handleSubmit" class="q-gutter-y-lg q-mx-md q-ma-lg">
      <!-- Course name -->
      <q-input
        v-model="form.name"
        label-color="orange"
        label="Course Name *"
        filled
        :rules="[rules.required, rules.max255]"
      />

      <!-- Course description -->
      <q-input
        v-model="form.description"
        label-color="orange"
        label="Course Description"
        filled
      />

      <!-- Course Starting On -->
      <q-input 
        filled 
        v-model="form.startAt" 
        label-color="orange"
        label="Course Starting On *"
        :rules="[rules.required]"
      >
        <template v-slot:prepend>
          <q-icon name="event" class="cursor-pointer" color="teal">
            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
              <q-date v-model="form.startAt" mask="YYYY-MM-DD HH:mm">
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-date>
            </q-popup-proxy>
          </q-icon>
        </template>

        <template v-slot:append>
          <q-icon name="access_time" class="cursor-pointer" color="teal">
            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
              <q-time v-model="form.startAt" mask="YYYY-MM-DD HH:mm" format24h>
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-time>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>

      <!-- Course Ending On -->
      <q-input 
        filled 
        v-model="form.endAt" 
        label-color="orange"
        label="Course Ending On *"
        :rules="[rules.required]"
      >
        <template v-slot:prepend>
          <q-icon name="event" class="cursor-pointer" color="teal">
            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
              <q-date v-model="form.endAt" mask="YYYY-MM-DD HH:mm">
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-date>
            </q-popup-proxy>
          </q-icon>
        </template>

        <template v-slot:append>
          <q-icon name="access_time" class="cursor-pointer" color="teal">
            <q-popup-proxy cover transition-show="scale" transition-hide="scale">
              <q-time v-model="form.endAt" mask="YYYY-MM-DD HH:mm" format24h>
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-time>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>

      <!-- Course Status -->
      <q-select
        class="primary-shadow"
        label-color="orange"
        dark
        bg-color="dark"
        v-model="form.statusTypeId"
        :options="initStore.courseStatusTypes"
        option-label="typeName"
        option-value="id"
        emit-value
        map-options
        label="Course Status Type"
        :behavior="$q.screen.lt.md ? 'dialog' : 'menu'"
        popup-content-class="teal-dropdown"
        filled
        :rules="[rules.required]"
      />

      <!-- Action buttons -->
      <q-card-actions align="right" class="q-pt-md">
        <!-- Dismiss just closes the dialog -->
        <q-btn
          label="Dismiss"
          color="orange"
          text-color="white"
          unelevated
          class="q-px-xl no-border-radius"
          style="min-width: 100px"
          v-close-popup
        />
        <!-- Submit triggers form validation + emit -->
        <q-btn
          label="Submit"
          type="submit"
          color="teal"
          text-color="white"
          unelevated
          class="q-px-lg q-py-sm no-border-radius q-ml-sm"
          style="min-width: 140px"
        />
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { validationRules as rules } from 'src/validation/genericRules.js'
import { useInitStore } from 'src/stores/initStore.js'

//Stores
const initStore = useInitStore()

// Props: parent provides modelValue and edit mode flag
const props = defineProps({
  modelValue: { type: Object, required: true },
  isEdit: { type: Boolean, default: false },
})

// Events: update parent modelValue (v-model) and submit final payload
const emits = defineEmits(['update:modelValue', 'submit'])

// Local reactive copy of form data (decouples child from parent)
const form = reactive(JSON.parse(JSON.stringify(props.modelValue)))

/**
 * Keep form synced with parent prop (two-way binding)
 * - Vue 3.3 seems to have an easier way to do this using new defineModel approach
 * - maybe i'll update this in the future :)
 */
watch(
  () => props.modelValue,
  (val) => Object.assign(form, val),
  { deep: true }
)

watch(form, (val) => emits('update:modelValue', val), { deep: true })

// Submit handler: validate via Quasar, then emit full form payload
function handleSubmit() {
  emits('submit', { ...form })
}
</script>
