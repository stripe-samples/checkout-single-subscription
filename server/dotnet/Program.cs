using Microsoft.Extensions.FileProviders;
using Stripe;

DotNetEnv.Env.Load();

StripeConfiguration.AppInfo = new AppInfo
{
    Name = "stripe-samples/checkout-single-subscription",
    Url = "https://github.com/stripe-samples/checkout-single-subscription",
    Version = "0.0.1",
};

StripeConfiguration.ApiKey = Environment.GetEnvironmentVariable("STRIPE_SECRET_KEY");

var builder = WebApplication.CreateBuilder(args);
builder.Services.Configure<StripeOptions>(options =>
{
    options.PublishableKey = Environment.GetEnvironmentVariable("STRIPE_PUBLISHABLE_KEY");
    options.SecretKey = Environment.GetEnvironmentVariable("STRIPE_SECRET_KEY");
    options.WebhookSecret = Environment.GetEnvironmentVariable("STRIPE_WEBHOOK_SECRET");
    options.BasicPrice = Environment.GetEnvironmentVariable("BASIC_PRICE_ID");
    options.ProPrice = Environment.GetEnvironmentVariable("PRO_PRICE_ID");
    options.Domain = Environment.GetEnvironmentVariable("DOMAIN");
});

builder.Services.AddControllers().AddNewtonsoftJson();

var app = builder.Build();

if (app.Environment.IsDevelopment())
{
    app.UseDeveloperExceptionPage();
}

app.UseStaticFiles(new StaticFileOptions()
{
    FileProvider = new PhysicalFileProvider(
        Path.Combine(Directory.GetCurrentDirectory(),
        Environment.GetEnvironmentVariable("STATIC_DIR"))
    ),
    RequestPath = new PathString("")
});

app.UseRouting();

app.MapGet("/", () => Results.Redirect("index.html"));

app.MapControllers();

app.Run();