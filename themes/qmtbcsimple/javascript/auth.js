import 'firebaseui/dist/firebaseui.css';
import firebase from "firebase/app";
import 'firebase/auth';
import 'firebase/functions';
var firebaseui = require('firebaseui');
const firebaseConfig = {
  apiKey: 'AIzaSyDutzpQ1QwH8H3JadcVYJTVynJp8QHnptQ',
  authDomain: 'qmtbc-dev.firebaseapp.com',
  databaseURL: 'https://qmtbc-dev.firebaseio.com',
  projectId: 'qmtbc-dev',
  storageBucket: 'qmtbc-dev.appspot.com',
  messagingSenderId: '124651750232',
  appId: '1:124651750232:web:39c08e2360ea6600',
  tenantId: 'qmtbc'
};
firebase.initializeApp(firebaseConfig);

var functions = firebase.functions();

var ui = new firebaseui.auth.AuthUI(firebase.auth());
var uiConfig = {
  callbacks: {
    signInSuccessWithAuthResult: function(authResult, redirectUrl) {
      console.log('auth success');
      // console.log('authResult', authResult);
      document.getElementById('loader').style.display = 'block';

      const result = fetch('firebaselogin/loginResult/'+authResult.user.Aa).then(()=>{
        window.location = redirectUrl;
      })
      // console.log('firebaselogin', result);
      // console.log('redirectUrl', redirectUrl); 

      // asdasd
      // await new Promise(r => setTimeout(r, 7000));
      // return true;
    },
    uiShown: function() {
      // The widget is rendered.
      // Hide the loader.
      document.getElementById('loader').style.display = 'none';
    }
  },
  // Will use popup for IDP Providers sign-in flow instead of the default, redirect.
  signInFlow: 'popup',
  signInSuccessUrl: 'http://localhost/qmtbc/new',
  signInOptions: [
    // Leave the lines as is for the providers you want to offer your users.
    // firebase.auth.GoogleAuthProvider.PROVIDER_ID,
    // firebase.auth.FacebookAuthProvider.PROVIDER_ID,
    // firebase.auth.TwitterAuthProvider.PROVIDER_ID,
    // firebase.auth.GithubAuthProvider.PROVIDER_ID,
    firebase.auth.EmailAuthProvider.PROVIDER_ID,
    // firebase.auth.PhoneAuthProvider.PROVIDER_ID
  ],
  // // Terms of service url.
  // tosUrl: '<your-tos-url>',
  // // Privacy policy url.
  // privacyPolicyUrl: '<your-privacy-policy-url>'
};

if (document.getElementById("firebaseui-auth-container")) {
  console.log('ui auth init')
  ui.start('#firebaseui-auth-container', uiConfig);
  
}
    // "tenantCode" : "qmtbc",
    // "email" : "aldoushuxley@wekasolutions.co.nz",
    // "password" : "wekaT3st"
