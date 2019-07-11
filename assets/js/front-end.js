document.addEventListener('DOMContentLoaded', (e) => {
  var app = new Vue({
    el : "#cps_search_module",
    data () {
      return {
        posts : null,
        none_found : false,
        filtered_posts: null,
        ready : false,
        message : "No posts found!",
        search_term : "",
        search_by : "",
        settings : {
          post_type : null,
          template : null,
          searchable_metas : null,
        },
      }
    },
    mounted() {
      let ajaxurl = ajax_object.ajaxurl;
      let data = new FormData();
      data.append('action','get_cps_settings')
      axios.post(ajaxurl, data)
        .then(response => (this.settings = response.data))
        .catch(function (error) {
          console.log(error);
      });
      data = new FormData();
      data.append('action', 'get_cps_posts');
      axios.post(ajaxurl, data)
        .then(response => (this.posts = response.data))
        .catch(error => console.log(error))
        .then(() => this.posts.forEach(post => (post.show = true)))
        .then(() => this.ready = true)
    },
    methods : {
      filter () {
        let posts_shown = 0;
        if (this.search_by === "") {
          this.posts.forEach( post => {
            post.show = false;
            for ( var key in post ) {
              if (typeof post[key] === "string") {
                if (post[key].toLowerCase().includes(this.search_term.toLowerCase()) && !post.show) {
                  post.show = true;
                  posts_shown ++;
                }
              }
            }
          })
        } else {
          this.posts.forEach( post => {
            post.show = false;
            if (typeof post[this.search_by] === "string") {
              if (post[this.search_by].toLowerCase().includes(this.search_term.toLowerCase())) {
                post.show = true;
                posts_shown ++;
              }
            }
          })
        }
        this.none_found = posts_shown === 0;
      },
    }
  })
})
