App.js

import React, { Component } from 'react';
import PostForm from './PostForm';
import AllPost from './AllPost';


class App extends Component {
render() {
return (
<div className="App">
  <div className="navbar">
    <h2 className="center ">Post It</h2>
    </div>
    <PostForm />
    <AllPost />
</div>
);
}
}
export default App;
view rawApp.js hosted with ❤ by GitHub

PostForm.js


import React, { Component } from 'react';
import { connect } from 'react-redux';
class PostForm extends Component {
handleSubmit = (e) => {
e.preventDefault();
 const title = this.getTitle.value;
 const message = this.getMessage.value;
 const data = {
  id: new Date(),
  title,
  message,
  editing: false
 }
 this.props.dispatch({
 type: 'ADD_POST',
 data
 })
 this.getTitle.value = '';
 this.getMessage.value = '';
}
render() {
return (
<div className="post-container">
  <h1 className="post_heading">Create Post</h1>
  <form className="form" onSubmit={this.handleSubmit} >
   <input required type="text" ref={(input) => this.getTitle = input}
   placeholder="Enter Post Title" /><br /><br />
   <textarea required rows="5" ref={(input) => this.getMessage = input}
   cols="28" placeholder="Enter Post" /><br /><br />
   <button>Post</button>
  </form>
</div>
);
}
}
export default connect()(PostForm);
view rawPostForm.js hosted with ❤ by GitHub

Post.js

import React, { Component } from 'react';
import { connect } from 'react-redux';
class Post extends Component {
render() {
return (
<div className="post">
  <h2 className="post_title">{this.props.post.title}</h2>
  <p className="post_message">{this.props.post.message}</p>
  <div className="control-buttons">
    <button className="edit"
    onClick={() => this.props.dispatch({ type: 'EDIT_POST', id: this.props.post.id })
    }
    >Edit</button>
    <button className="delete"
    onClick={() => this.props.dispatch({ type: 'DELETE_POST', id: this.props.post.id })}
    >Delete</button>
  </div>
</div>
);
}
}
export default connect()(Post);
view rawPost.js hosted with ❤ by GitHub

AllPost.js


import React, { Component } from 'react';
import { connect } from 'react-redux';
import Post from './Post';
import EditComponent from './EditComponent';
class AllPost extends Component {
render() {
return (
<div>
  <h1 className="post_heading">All Posts</h1>
  {this.props.posts.map((post) => (
  <div key={post.id}>
    {post.editing ? <EditComponent post={post} key={post.id} /> : <Post post={post}
    key={post.id} />}
  </div>
))}
</div>
);
}
}

const mapStateToProps = (state) => {
return {
posts: state
}
}
export default connect(mapStateToProps)(AllPost);
view rawAllPost.js hosted with ❤ by GitHub

EditComponent.js

import React, { Component } from 'react';
import { connect } from 'react-redux';


class EditComponent extends Component {
handleEdit = (e) => {
  e.preventDefault();
  const newTitle = this.getTitle.value;
  const newMessage = this.getMessage.value;
  const data = {
    newTitle,
    newMessage
  }
  this.props.dispatch({ type: 'UPDATE', id: this.props.post.id, data: data })
}
render() {
return (
<div key={this.props.post.id} className="post">
  <form className="form" onSubmit={this.handleEdit}>
    <input required type="text" ref={(input) => this.getTitle = input}
    defaultValue={this.props.post.title} placeholder="Enter Post Title" /><br /><br />
    <textarea required rows="5" ref={(input) => this.getMessage = input}
    defaultValue={this.props.post.message} cols="28" placeholder="Enter Post" /><br /><br />
    <button>Update</button>
  </form>
</div>
);
}
}

postReducer.js

const postReducer = (state = [], action) => {
switch (action.type) {
case 'ADD_POST':
return state.concat([action.data])
case 'DELETE_POST':
return state.filter((post) => post.id !== action.id)
case 'EDIT_POST':
return state.map((post) => post.id === action.id ? { ...post, editing: !post.editing } : post)
case 'UPDATE':
return state.map((post) => {
if (post.id === action.id) {
return {
...post,
title: action.data.newTitle,
message: action.data.newMessage,
editing: !post.editing
}
} else return post;
})
default:
return state;
}
}
export default postReducer;
view rawpostReducer.js hosted with ❤ by GitHub

export default connect()(EditComponent);
view rawEditComponent.js hosted with ❤ by GitHub