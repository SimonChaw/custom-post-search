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
        selected : null,
        updating : false,
        messageShow : false,
        extendedUpdating : false,
        extendedMessageShow : false,
      }
    },
    mounted () {
      let data = new FormData();
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
        if (!this.updating) {
          this.updating = true;
          let data = new FormData();
          data.append('action', 'save_cps_settings');
          this.settings.template = encodeURIComponent(document.querySelector('#edit').value);
          for ( var key in this.settings ) {
              data.append(key, this.settings[key]);
          }
          axios
            .post(ajaxurl, data)
            .then( response => {
              if (response.data.success === true) {
                this.messageShow = true;
                setTimeout(() => { this.messageShow = false }, 5000);
              }
            })
            .catch(function (error) {
              console.log(error);
            })
            .then(() => (this.updating = false))
        }
      },
      updateCPSExtendedSettings () {
        if (!this.extendedUpdating) {
          this.extendedUpdating = true;
          let data = new FormData();
          data.append('action', 'update_cps_extended_settings');
          data.append('extended_settings', encodeURIComponent(JSON.stringify(this.settings.searchable_metas)));
          axios
            .post(ajaxurl, data)
            .then( response => {
              if (response.data.success === true) {
                this.extendedMessageShow = true;
                setTimeout(() => { this.extendedMessageShow = false }, 5000);
              }
            })
            .catch( error => {
              console.log(error);
            })
            .then(() => (this.extendedUpdating = false))
        }
      }
    }
  })


})
