"use client";

import { useEffect, useState } from "react";
import { useSearchParams } from "next/navigation";
import Link from "next/link";
import { Suspense } from "react";

function SuccessContent() {
  const searchParams = useSearchParams();
  const sessionId = searchParams.get("session_id");
  const [session, setSession] = useState<object | null>(null);

  useEffect(() => {
    if (sessionId) {
      fetch(`/api/checkout-session?sessionId=${sessionId}`)
        .then((res) => res.json())
        .then((data) => setSession(data))
        .catch((err) => console.error("Error fetching session:", err));
    }
  }, [sessionId]);

  return (
    <main className="flex min-h-screen flex-col items-center justify-center p-8">
      <div className="w-full max-w-md">
        <div className="bg-white p-8 rounded-lg shadow-md">
          <h1 className="text-2xl font-bold mb-4 text-green-600">
            Your payment succeeded
          </h1>
          <p className="text-gray-600 mb-4">View CheckoutSession response:</p>

          <div className="bg-stripe-light p-4 rounded-md overflow-auto max-h-64 mb-6">
            <pre className="text-xs">
              {session ? JSON.stringify(session, null, 2) : "Loading..."}
            </pre>
          </div>

          <div className="space-y-3">
            <Link
              href="/"
              className="block w-full bg-stripe-purple text-white py-3 rounded-md font-semibold hover:bg-opacity-90 transition-colors text-center"
            >
              Restart demo
            </Link>

            <form action="/api/customer-portal" method="POST">
              <input type="hidden" name="sessionId" value={sessionId || ""} />
              <button
                type="submit"
                className="w-full bg-stripe-dark text-white py-3 rounded-md font-semibold hover:bg-opacity-90 transition-colors"
              >
                Manage Billing
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  );
}

export default function SuccessPage() {
  return (
    <Suspense fallback={<div className="flex min-h-screen items-center justify-center">Loading...</div>}>
      <SuccessContent />
    </Suspense>
  );
}
