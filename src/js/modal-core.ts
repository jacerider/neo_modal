/**
 * The modal code proper: the NeoModal class and what it needs.
 *
 * This is the `neo_modal/modal.core` library, which nothing attaches at render
 * time. The stub in `modal.ts` (`neo_modal/modal`) loads it when a visitor
 * shows intent to open a modal, or when something calls the API, and the
 * escape hatch makes `neo_modal/modal` depend on it so it loads up front.
 *
 * Importing the class is all this file does: the class publishes itself as
 * `window.NeoModal`, and that global is how the stub learns the code is here,
 * whichever of the two ran first.
 */
import './modal/modal';
