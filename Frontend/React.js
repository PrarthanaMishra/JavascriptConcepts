import { link } from "fs";

Date: 5th march 2019
Finished 63rd video. Styling dynamically

6th march 2019
Radium is a package for react which allows to use inline styles with  psudeoselectors and media queries. Explore more about psudeo selectors and media queries. 
WebPack : It’s a bundling tool that do all sorts of optimizations and takes care of css file, parse css imports.
Babel : For compiling ES6 to ES5
Video 66 important as it edit the built in configuration.

16th March, 2019
Things that should be in mind while making components : -
Only for global css trying to wrap some element than NO
If the given component will do some specific tasks and will be reused somewhere than yes
Component or container that maintains state should have the code to render the things. It should have least JSX.

18th March, 2019
There should be only one component that handles state others should be only functional component. 
If we want to pass handler in the functional component we can do it by sending them in
Props. Send them in properties. Whatever we will send it will get accessed using props

19th March, 2019
Container can have props argument as well. That is app.js but in container that maintain state cannot acess state directly, can access using this.props or this.state.
Props will pass in index.js so the actual rendering is done in index.js and then 
app.js will get props passed in index.js
    3.  Stateful component will be created using class and extends Component while functional
	Component won’t have a class
    4. Every stateful component needs to have a render method.
     5. Lifecycyle of different components hooks. First constructor gets executed than componentWillMount, then render and then componentDidMount. So, in render method if it’s rendering other component which has all three things it will execute in order so in that render if there is a component it will exceute it’s all four things and than componentDidMount will get
Execute. It’s same as recursion. The statement after function call will exceute until all functions
Returns something.

21st march, 2019
Component has shouldUpdateComponent, while real component has these features in built. So, these methods checks whether the properties in react in changed, if yes
Then only it renders react DOM otherwise not. In some case react DOM only renders, not the real browser DOM.
Q: How react decides to update real DOM?
    It has two copies of virtual DOM so on rendering it checks whether the old virtual DOM is same as new virtual DOM. If yes it doesn’t render the real DOM. If no, that is if there is a change it only render the actual component that is changed not the entire DOM
This makes react fast.
We can return array of html tags but not object of html tags, To solve that we can aux component which only return props.children
Aux component is higher order component
Npm install --save prop-types 
Add component.propTypes = {
‘Click’: ‘’,
}
22nd march, 2019
References can only be in stateful component. It will give reference to the input
React 16.3 has new features like ref, createAuthenticate

Building a Burger project
Planning : - Has to add some hash code in  config.js file
If we change in config .js file we need to run npm start 
26th march, 2019

6th May, 2019
Start with video no-127
Hackernoon great site to know.
https://www.youtube.com/watch?v=3aJI1ABdjQk - 

React.js video link
Link : https://www.youtube.com/watch?v=-40p_dZccPg





