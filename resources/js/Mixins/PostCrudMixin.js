import axios from "axios";
import {eventBus} from "../app";
import router from "../router";

export const PostCrudMixin = {
    data() {
        return {
            posts: null,
        }
    },
    computed: {},
    methods: {
        getComments(post_id) {
            const post = this.posts.find(post => post.details.id === post_id);
            const url = this.route('network.comment.index', {
                'timeline': post.details.timeline_id,
                'post': post_id,
            });
            return axios.get(url)
        },
        updateComment(payload) {
            /*=== We get comments by finding the post with id then we have access to comments and we can update ===*/
            const comments = this.posts.find(post => post.details.id === payload.postId).comments;
            const index = comments.indexOf(payload.old);
            this.$set(comments, index, payload.new);
        },
        addComment(post_id) {
            this.refreshComments(post_id)
        },
        removeComment(payload) {
            const comments = this.posts.find(post => post.details.id === payload.postId).comments;
            /*=== add filler comment ===*/
            if (payload.filler) {
                comments.push(payload.filler);
            }

            /*=== remove comment from data ===*/
            const index = comments.indexOf(payload.old);
            comments.splice(index, 1);

            /*=== Information ===*/
            this.posts.find(post => post.details.id === payload.postId).comments_count --;
        },
        refreshComments(post_id) {
            const post = this.posts.find(post => post.details.id === post_id);
            this.getComments(post_id)
                .then(res => {
                    post.comments = res.data.data;
                    post.comments_count = res.data.comments_count;
                });
        },
        addPost(payload) {
            this.loadPosts();
        },
        removePost(payload) {
            /*=== Add a filler post to end so we dont lose the track of infinite scrolling ===*/
            if (payload.filler) {
                this.posts.push(payload.filler);
            }

            /*=== Remove post from data ===*/
            const index = this.posts.indexOf(payload.old);
            if (index !== -1) this.posts.splice(index, 1);
        },
        updatePost(argument) {
            const index = this.posts.indexOf(argument.item);
            this.$set(this.posts, index, argument.payload);
        },
        infiniteHandler($state) {
            const url = this.links.next;
            axios.get(url)
                .then(res => {
                    this.posts.push(...res.data.data);
                    this.links = res.data.links;
                    $state.loaded();

                    if (res.data.meta.current_page === res.data.meta.last_page) {
                        $state.complete();
                    }
                })
        }
    },
    mounted() {
        eventBus.$on('post-updated', this.updatePost);
        eventBus.$on('comment-created', this.addComment);
        eventBus.$on('comment-updated', this.updateComment);
    }
};
