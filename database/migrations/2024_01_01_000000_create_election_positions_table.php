use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElectionPositionsTable extends Migration
{
    public function up()
    {
        Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_settings_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('required_votes')->default(1);
            $table->integer('max_candidates')->default(0);
            $table->date('minimum_member_since_date')->nullable();
            $table->json('eligible_roles')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('election_positions');
    }
} 