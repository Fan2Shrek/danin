import tokens from './tokens.ts';

const translations: { [key: string]: string } = {
    [tokens.login.title]: 'Connexion',
    [tokens.login.email]: 'E-mail',
    [tokens.login.password]: 'Mot de passe',
    [tokens.login.submit]: 'Se connecter',
    [tokens.login.register.link]: "Vous n'avez pas de compte ?",
    [tokens.login.register.cta]: 'Créer un compte',
    [tokens.login.error.invalid]: 'Nom d’utilisateur ou mot de passe invalide',
    [tokens.login.error.empty]: 'Veuillez remplir tous les champs',
    [tokens.footer.copyright]: 'Tous droits réservés',
    [tokens.footer.links.games.title]: 'Jeux',
    [tokens.footer.links.games.supportedList]: 'Jeux supportés',
    [tokens.footer.links.games.suggestion]: 'Proposer un jeu',
    [tokens.footer.links.documentation.title]: 'Documentation',
    [tokens.footer.links.documentation.createRoom]: 'Créer une salle',
    [tokens.footer.links.documentation.connectGame]: 'Connecter un jeu',
    [tokens.footer.links.documentation.connectTchat]: 'Connecter un tchat',
    [tokens.footer.links.currentGames.title]: 'Salles en cours',
    [tokens.footer.support.title]: 'Nous soutenir',
};

export default translations;
