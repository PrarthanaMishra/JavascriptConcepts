https://github.com/PrarthanaMishra/reactjs-interview-questions

1. Ajax request is made in componentDidMount(). That is api request is made in componentDidMount() life cycle
2. componentDidUpdate - It is used to update DOM if some props or state changes.
36. Proxy props      return <WrappedComponent {...this.props} {...newProps} />
37. Context tree
38. React.Children.count this.props.children - this.prop.children
47. What are frafments- it useful when tree is very deep. Fragment doesn't create extra node in DOM
48. Portals - 
49. But unless you need to use a lifecycle hook in your components, you should go for function components
50. These stateful components are always class components and have a state that gets initialized in the constructor.
51. prop-types is used to check types of prop being used
54. componentDidCatch to log errors
57. The react-dom package provides DOM-specific methods that can be used at the top level of your app.like
    render(),hydrate(),unmountComponentAtNode(),findDOMNode(),createPortal()
58. ReactDOMServer - renderToString for server side rendering
60. dangerouslySetInnerHTML - react's inner html- to change value of html tags 
65. componentWillMount - It is called before render(), therefore setting state in this method will not trigger a re-render
67. conditional short-circuiting to render a given part of your component
69. decorarors - Decorators are flexible and readable way of modifying component functionality.
70. How do you memoize a component?
71. How you implement Server Side Rendering or SSR?
74. What is the lifecycle methods order in mounting?
79. What is the recommended way for naming components?
81. What is a switching component?
83. What is strict mode in React?
84. What are React Mixins?
86. What are the Pointer Events supported in React?
88. Are custom DOM attributes supported in React v16?
90. Can you force a component to re-render without calling setState? - components renders on calling set state
91. What is the difference between super() and super(props) in React using ES6 classes?
94. What is React proptype array with shape?
100. How to re-render the view when the browser is resized?
106. Why you can't update props in React?
111. What are the approaches to include polyfills in your create-react-app?
112. How to use https instead of http in create-react-app?
114. How to add Google Analytics for React Router?
119. Why is a component constructor called only once?
123. What are the common folder structures for React?
129. 
React Router
130. How React Router is different from history library?
133. https://til.hashrocket.com/posts/fzip6gccfa-use-decorators-for-react-higher-order-components
    decoraors are the way to wrap a component inside a function
134. How to get query parameters in React Router v4?
136. How to pass params to history.push method in React Router v4?
138. How to get history on React Router v4?

React Internationalization
140. What is React Intl?
React Redux
152. What is flux? - It is not a framework or a library but a new kind of architecture that complements React and the concept of Unidirectional Data Flow 
156. What is the difference between mapStateToProps() and mapDispatchToProps()?
161. How to dispatch an action on load?
162. How to use connect() from React Redux?
163. How to reset state in Redux?
167. How to make AJAX request in Redux?
170. What is the difference between component and container in React Redux?
redux-thunk
Here are middleware every api call goes in sequence
const middleware = [
    thunkMiddleware(),
    clientMiddleware(helpers),
    routerMiddleware(history),
    gaMiddleware(),
    userMiddleware(),
    paymentsMiddleware(),
    notifyMiddleware()
  ];

Adding debugger in vs code
https://medium.com/@auchenberg/live-edit-and-debug-your-react-apps-directly-from-vs-code-without-leaving-the-editor-3da489ed905f

25th April,2019

1. import cart from './modules/cart';
    this type of import means cart must be a default import in the cart file.
    we import default import like this. Other imports have some syntax of doing it
2.