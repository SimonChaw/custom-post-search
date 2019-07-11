document.addEventListener('DOMContentLoaded', (e) => {
  let editor;
  var app = new Vue({
    el : "#app",
    data () {
      return {
        postTypes : null,
        settings : {
          post_type : null,
          template : null,
        },
        selected : null
      }
    },
    mounted () {
      let data = new FormData();
      console.log(editor);
      data.append('action','get_cps_settings')
      axios
        .get(window.location.origin + '/wp-json/wp/v2/types')
        .then(response => (this.postTypes = response.data));
      axios.post(ajaxurl, data)
        .then(response => (this.settings = response.data))
        .then(() => document.querySelector('#edit').value = this.settings.template)
        .catch(function (error) {
          console.log(error);
        });

    },
    methods : {
      updateCPSSettings () {
        let data = new FormData();
        data.append('action', 'save_cps_settings');
        this.settings.template = encodeURIComponent(document.querySelector('#edit').value);
        for ( var key in this.settings ) {
            data.append(key, this.settings[key]);
        }
        axios
          .post(ajaxurl, data)
          .then(function (response) {
            console.log(response);
          })
          .catch(function (error) {
            console.log(error);
          })
      },
      updateCPSExtendedSettings () {
        let data = new FormData();
        data.append('action', 'update_cps_extended_settings');
        data.append('extended_settings', encodeURIComponent(JSON.stringify(this.settings.searchable_metas)));
        axios
          .post(ajaxurl, data)
          .then( response => {
            console.log(response);
          })
          .catch( error => {
            console.log(error);
          })
      }
    }
  })


})
