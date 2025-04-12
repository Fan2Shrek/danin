import tokens from './tokens.ts'

const translations: { [key: string]: string } = {
    [tokens.login.title]: 'Connexion',
    [tokens.login.username]: "Nom d'utilisateur",
    [tokens.login.password]: 'Mot de passe',
    [tokens.login.submit]: 'Se connecter',
    [tokens.login.register.link]: "Vous n'avez pas de compte ?",
    [tokens.login.register.cta]: 'Créer un compte',
}

export default translations
