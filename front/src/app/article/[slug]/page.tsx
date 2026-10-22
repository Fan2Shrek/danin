'use client';

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';

import { getStoreState } from '@/lib/apiStore';
import { useLocale } from '@/context/LocaleContext';

import type { Article } from '@/lib/api/resources/article';

export default function ArticlePage() {
    const { slug } = useParams<{ slug: string }>();
    const { locale } = useLocale();
    const [article, setArticle] = useState<Article | null>(null);

    useEffect(() => {
        (async () => {
            setArticle(await getStoreState<Article>(locale, `article_${slug}`));
        })();
    }, [locale, slug]);

    if (!article) {
        return null;
    }

    return (
        <article className="mx-auto max-w-3xl px-6 py-16">
            <h1 className="mb-6 text-3xl font-bold text-slate-900 dark:text-white">{article.title}</h1>
            <div
                className="space-y-4 leading-relaxed text-slate-700 [&_a]:text-indigo-600 [&_a]:underline [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-slate-900 dark:text-slate-300 dark:[&_h2]:text-white"
                dangerouslySetInnerHTML={{ __html: article.content }}
            />
        </article>
    );
}
