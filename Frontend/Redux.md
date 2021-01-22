7th of october 2019

1. mapstateToProps  - this is a bad practise. 
2. mapDispatchToProps - It bind action to props. Action can be function as well.
3. Connect - components can talk to store directly so this command connects components to store.
4. bindActionCreators - all action creators has second parameters as dispatch, so if we have multiple action creators we    have this function that will bind all of them.
    Example : import { bindActionCreators } from "redux";

    const increment = () => ({ type: "INCREMENT" });
    const decrement = () => ({ type: "DECREMENT" });
    const reset = () => ({ type: "RESET" });

    // binding an action creator
    // returns (...args) => dispatch(increment(...args))
    const boundIncrement = bindActionCreators(increment, dispatch);

    // binding an object full of action creators
    const boundActionCreators = bindActionCreators({ increment, decrement, reset }, dispatch);
    // returns
    // {
    //   increment: (...args) => dispatch(increment(...args)),
    //   decrement: (...args) => dispatch(decrement(...args)),
    //   reset: (...args) => dispatch(reset(...args)),
// }
5. redux-persist(persistCombineReducers) - To store your logs or store at persisited storage accross all sessions. 
    Redux Persist takes your Redux state object and saves it to persisted storage. Then on app launch it retrieves this persisted state and saves it back to redux.
6.replaceReducer(nextReducer) - Replaces the reducer currently used by the store to calculate the state.It is an            advanced API. You might need this if your app implements code splitting, and you want to load some of the reducers       dynamically. You might also need this if you implement a hot reloading mechanism for Redux.
7.compose(...functions) - Composes functions from right to left.
    (arguments): The functions to compose. Each function is expected to accept a single parameter. Its return value will be provided as an argument to the function standing to the left, and so on. The exception is the right-most argument which can accept multiple parameters, as it will provide the signature for the resulting composed function.
8.

14th Dec, 2019
Some useful points:-
1. We could have javascript global object as state but we didn't because react doesn'r react with global object change.
2. Redux doesn't allow to manipulate javascrit state(centre store) directlty. If it does it will be difficult to know from where the error occurs if anything gets changed. it will make it unpredictable. From where this is done.
3.So there should a process that can only change the state of react.
4. Process is there is action supppose addtoCart this is a javascript action that will be sent to store it contains type and payload. When it goes to store it is taken by reducer and then on the basis of type reducer
chnages the values in the store. Reducer can have only synchronous. when states gets chagned now hwo the chnage gets back to componenets? as store changes it triggers all the subscription associated with it.
And components gets subscribed to store updates and changes automatically. This is the whole process.
Let me check it for addToCart.
check - AddToCart flow.
button clicked - it goes t which component - container - api call - action - reducer - suscription- component chnaged
Listing component - calling addtocart - 

frontend/src/components/Listing/index.js - calling frontend/src/components/addToCart/
it goes to addtocart component and then addtocart has 
onClick={onClick(itemId, sku, simpleSku, session, pincode)(addToCart)} event which calls the api written in redux module. apicalls are mentioned in redux api
Don't know how it gets access of redux function it havent imported in the file

Got the solution of types. React goes to client middleware for promise based call. And then it will take 
type on the basis of promise success and fail. and reactthunk

3rd January:-
provider will wrap everything.
4th Jan
How react gets connected to redux store
1. Wrap the whole react app with store using provider.
2. React compoonent use connect to get props.
3. React containers use connect to get state.
4 If we want to get props or state from store we will have to use connect for that.
5. If your React application uses multiple Redux stores, connect() allows you to easily specify which store a container component should be connected to.
const mapStateToProps = (state) => {
     return { things: state.things }
};
export default connect(mapStateToProps)(MyComponent);
state argument can destructed while calling mapStateToprops
The above sentence means whatever value returned by mapStateToProps written above will be used by MyComponent. Ans since myComponent is connected with the store so if anything gets chnaged in store it will get reflected in this compoonent.
Analogous to “reading” data, mapStateToProps gets the data that is fed to its component.
But what if the component wants change the state?
That is where mapDispatchToProps comes in.
const mapDispatchToProps = () => {
     return {
          addThing: addThing,
          doAnotherThing: doAnotherThing
     }
}
Whatever mapStateToProps(things) and mapDispatchToProps(addThing) will be available in react as a prop.
this.prop.things or this.prop.addThing
Firebase- what is this? 

6th jan, 2019
Redux middleware are hit whe we dispatch action and before it reach reducer.
applyMiddleware is used to apply dlware  in redux.
Redux developer tool is awesome. You can track dispatch action.Dirty your handes
Action creators are used to implement asynchronous code
In our project AddToCart component is used in listing component. AddToCart componenent is called with few paramters in from lisitng and rest of the parameters comes either from action creators or props.
When function is called in destructring it sequence of parameters doesn't matter only name of the varable matters.
redux-thunk - allows to rispatch action implcitly
Note: Reducer only handle synchronous action. Asynchhhrnous actions are maintained by thunk middleware.
So middleware wait till achnrous functions completes and then sends asynchrous code.
Make a different file for actionTypes action creators. Actions types which contains constant variable for actions while action creators are used to create actions which in turn use action types.
In the given project src/redux/cart.js - each component file contains actions, action creators and reducer as well in the same file. so these 3 are divided by components.
277 over

7th Jan 2019
Redial - It is life cycle management for react-router.
If we want to change response coming from async code. We should do it in reducer. Not much in action creator
281
redux doc- structuring reducers- immutable update patterns- Learn how to update state array and object immutablity. Has to go down deep levels.
In the given project 
312 video







