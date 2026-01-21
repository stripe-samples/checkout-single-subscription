import Link from "next/link";

export default function CanceledPage() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center p-8">
      <div className="w-full max-w-md">
        <div className="bg-white p-8 rounded-lg shadow-md text-center">
          <h1 className="text-2xl font-bold mb-6 text-red-600">
            Your payment was canceled
          </h1>

          <Link
            href="/"
            className="block w-full bg-stripe-purple text-white py-3 rounded-md font-semibold hover:bg-opacity-90 transition-colors"
          >
            Restart demo
          </Link>
        </div>
      </div>
    </main>
  );
}
