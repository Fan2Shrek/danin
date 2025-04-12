import tokens from './tokens.ts'

const translations: { [key: string]: string } = {
    [tokens.login.title]: 'Connexion',
    [tokens.login.email]: "E-mail",
    [tokens.login.password]: 'Mot de passe',
    [tokens.login.submit]: 'Se connecter',
    [tokens.login.register.link]: "Vous n'avez pas de compte ?",
    [tokens.login.register.cta]: 'Créer un compte',
    [tokens.login.error.invalid]: 'Nom d’utilisateur ou mot de passe invalide',
    [tokens.login.error.empty]: 'Veuillez remplir tous les champs',
}

export default translations
