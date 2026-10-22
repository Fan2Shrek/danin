import Link from 'next/link';

type Props = {
    text: string;
    link?: string;
    className?: string;
    onClick?: () => void;
    type?: 'button' | 'submit';
    disabled?: boolean;
};

export default function BasicButton({ text, link, className = '', onClick, type = 'button', disabled }: Props) {
    const classes = `inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-500 hover:shadow-indigo-500/30 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:shadow-none ${className}`;

    if (link) {
        return (
            <Link href={link} className={classes}>
                {text}
            </Link>
        );
    }

    return (
        <button type={type} onClick={onClick} disabled={disabled} className={classes}>
            {text}
        </button>
    );
}
