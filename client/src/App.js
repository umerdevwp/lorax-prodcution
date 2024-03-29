/**
 * Entry application component used to compose providers and render Routes.
 * */

import React from "react";

import {Provider} from "react-redux";
import {BrowserRouter} from "react-router-dom";
import {PersistGate} from "redux-persist/integration/react";
import {LastLocationProvider} from "react-router-last-location";
import {I18nProvider, LayoutSplashScreen, ThemeProvider} from "./_metronic";
import AgentAdminRoutes from './app/router/AgentAdminRoutes';

export default function App({store, persistor, basename}) {
    const splashScreen = document.getElementById("splash-screen");
    splashScreen.classList.add("hidden");
    return (

        /* Provide Redux store */
        <Provider store={store}>

            {/* Asynchronously persist redux stores and show `SplashScreen` while it's loading. */}
            <PersistGate persistor={persistor} loading={<LayoutSplashScreen/>}>
                {/* Add high level `Suspense` in case if was not handled inside the React tree. */}
                <React.Suspense fallback={<LayoutSplashScreen/>}>
                    {/* Override `basename` (e.g: `homepage` in `package.json`) */}
                    <BrowserRouter basename={basename}>
                        {/*This library only returns the location that has been active before the recent location change in the current window lifetime.*/}
                        <LastLocationProvider>
                            {/* Provide Metronic theme overrides. */}

                            <ThemeProvider>
                                {/* Provide `react-intl` context synchronized with Redux state.  */}
                                <I18nProvider>
                                    {/* Render routes with provided `Layout`. */}
                                        <AgentAdminRoutes/>
                                </I18nProvider>
                            </ThemeProvider>

                        </LastLocationProvider>
                    </BrowserRouter>
                </React.Suspense>
            </PersistGate>

        </Provider>


    );
}
