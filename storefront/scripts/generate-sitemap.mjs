import { writeFile } from "node:fs/promises";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

function escapeXml(value) {
  return value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&apos;");
}

export function buildSitemapXml(urls, options = {}) {
  const entries = urls
    .map((item) => {
      if (typeof item === "string") {
        if (!options.detailed) {
          return `  <url><loc>${escapeXml(item)}</loc></url>`;
        }
        return `  <url>\n    <loc>${escapeXml(item)}</loc>\n  </url>`;
      }
      const loc = `    <loc>${escapeXml(item.loc)}</loc>`;
      const lastmod = item.lastmod ? `\n    <lastmod>${escapeXml(item.lastmod)}</lastmod>` : "";
      const changefreq = item.changefreq ? `\n    <changefreq>${escapeXml(item.changefreq)}</changefreq>` : "";
      const priority = item.priority !== undefined ? `\n    <priority>${item.priority.toFixed(1)}</priority>` : "";
      const alternates = (item.alternates || [])
        .map(
          (alt) =>
            `\n    <xhtml:link rel="alternate" hreflang="${escapeXml(alt.hreflang)}" href="${escapeXml(alt.href)}" />`
        )
        .join("");
      return `  <url>\n${loc}${lastmod}${changefreq}${priority}${alternates}\n  </url>`;
    })
    .join("\n");

  const xhtmlNs = urls.some((u) => typeof u === "object" && u.alternates?.length)
    ? ' xmlns:xhtml="http://www.w3.org/1999/xhtml"'
    : "";

  return `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"${xhtmlNs}>\n${entries}\n</urlset>\n`;
}

async function fetchAllArticleIdentifiers(apiBase) {
  const identifiers = [];
  let page = 1;
  let hasMore = true;

  while (hasMore) {
    const response = await fetch(`${apiBase}/articles?page=${page}`);
    if (!response.ok) {
      throw new Error(`Failed to fetch articles page ${page}: ${response.status}`);
    }
    const body = await response.json();
    if (Array.isArray(body.data)) {
      identifiers.push(...body.data.map((article) => article.slug || article.id));
    }
    hasMore = Boolean(body.meta?.has_more || (body.meta?.last_page && page < body.meta.last_page));
    page += 1;
  }

  return identifiers;
}

async function main() {
  const apiBase = process.env.NUXT_PUBLIC_API_BASE || "https://admin.apconsulting.id/api";
  const siteUrl = process.env.NUXT_PUBLIC_SITE_URL || "https://apconsulting.id";
  const outputDir = process.env.SITEMAP_OUTPUT_DIR || resolve(__dirname, "../.output/public");
  const publicDir = resolve(__dirname, "../app/public");
  const today = new Date().toISOString().split("T")[0];

  const staticRoutes = [
    {
      loc: `${siteUrl}/`,
      lastmod: today,
      changefreq: "daily",
      priority: 1.0,
      alternates: [
        { hreflang: "id", href: `${siteUrl}/` },
        { hreflang: "en", href: `${siteUrl}/` },
        { hreflang: "x-default", href: `${siteUrl}/` },
      ],
    },
    {
      loc: `${siteUrl}/services`,
      lastmod: today,
      changefreq: "weekly",
      priority: 0.9,
      alternates: [
        { hreflang: "id", href: `${siteUrl}/services` },
        { hreflang: "en", href: `${siteUrl}/services` },
        { hreflang: "x-default", href: `${siteUrl}/services` },
      ],
    },
    {
      loc: `${siteUrl}/about`,
      lastmod: today,
      changefreq: "monthly",
      priority: 0.8,
      alternates: [
        { hreflang: "id", href: `${siteUrl}/about` },
        { hreflang: "en", href: `${siteUrl}/about` },
        { hreflang: "x-default", href: `${siteUrl}/about` },
      ],
    },
    {
      loc: `${siteUrl}/insight`,
      lastmod: today,
      changefreq: "daily",
      priority: 0.8,
      alternates: [
        { hreflang: "id", href: `${siteUrl}/insight` },
        { hreflang: "en", href: `${siteUrl}/insight` },
        { hreflang: "x-default", href: `${siteUrl}/insight` },
      ],
    },
    {
      loc: `${siteUrl}/contact`,
      lastmod: today,
      changefreq: "monthly",
      priority: 0.8,
      alternates: [
        { hreflang: "id", href: `${siteUrl}/contact` },
        { hreflang: "en", href: `${siteUrl}/contact` },
        { hreflang: "x-default", href: `${siteUrl}/contact` },
      ],
    },
  ];

  let articleIdentifiers = [];
  try {
    articleIdentifiers = await fetchAllArticleIdentifiers(apiBase);
  } catch (error) {
    console.warn(`[generate-sitemap] Could not fetch articles, sitemap will only contain static routes: ${error.message}`);
  }

  const articleRoutes = articleIdentifiers.map((id) => ({
    loc: `${siteUrl}/insight/${id}`,
    lastmod: today,
    changefreq: "monthly",
    priority: 0.7,
  }));

  const allRoutes = [...staticRoutes, ...articleRoutes];
  const xml = buildSitemapXml(allRoutes);

  // Write to both .output/public (if exists) and app/public (static source)
  try {
    await writeFile(resolve(publicDir, "sitemap.xml"), xml, "utf-8");
    console.log(`[generate-sitemap] Wrote app/public/sitemap.xml with ${allRoutes.length} URLs.`);
  } catch (e) {
    console.warn(`[generate-sitemap] Could not write to app/public: ${e.message}`);
  }

  try {
    await writeFile(resolve(outputDir, "sitemap.xml"), xml, "utf-8");
    console.log(`[generate-sitemap] Wrote .output/public/sitemap.xml with ${allRoutes.length} URLs.`);
  } catch (e) {
    // If .output/public doesn't exist yet (e.g. before generate), that's fine
  }
}

const isMain = process.argv[1] === fileURLToPath(import.meta.url);
if (isMain) {
  main().catch((error) => {
    console.error("[generate-sitemap] Failed:", error);
    process.exit(1);
  });
}
