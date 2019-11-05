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



