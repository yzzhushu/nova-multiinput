import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-multi-input', IndexField)
  app.component('detail-multi-input', DetailField)
  app.component('form-multi-input', FormField)
})
