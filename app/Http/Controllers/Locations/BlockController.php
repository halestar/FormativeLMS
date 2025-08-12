<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use App\Models\Schedules\Block;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class BlockController extends Controller implements HasMiddleware
{
    private static function errors(): array
    {
        return [
            'name' => __('errors.blocks.name'),
            'block_name' => __('errors.blocks.name'),
            'periods' => __('errors.blocks.periods'),
        ];
    }

    public function store(Request $request, Campus $campus)
    {
        Gate::authorize('create', Block::class, $campus);
        $data = $request->validate([
            'block_name' => 'required|max:20',
        ], static::errors());
        $block = new Block();
        $block->name = $data['block_name'];
        $block->campus_id = $campus->id;
        $block->save();
        return redirect(route('locations.blocks.edit', $block))
            ->with('success-status', __('locations.block.created'));
    }

    public function edit(Request $request, Block $block)
    {
        Gate::authorize('edit', $block);
        $breadcrumb =
            [
                trans_choice('locations.campus', 2) => route('locations.campuses.index'),
                $block->campus->name => route('locations.campuses.show', ['campus' => $block->campus_id]),
                __('locations.block.edit') => '#',
            ];
        return view('locations.blocks.edit', compact('block', 'breadcrumb'));
    }

    public function update(Request $request, Block $block)
    {
        Gate::authorize('edit', $block);
        $data = $request->validate([
            'name' => 'required|max:20',
            'periods' => 'required|array|min:1',
        ], static::errors());
        $block->name = $data['name'];
        $block->active = $request->input('active', false);
        $block->periods()->sync($data['periods']);
        $block->save();
        return redirect(route('locations.campuses.show', $block->campus_id))
            ->with('success-status', __('locations.block.updated'));
    }

    public function updateOrder(Request $request)
    {
        Gate::authorize('has-permission', 'locations.blocks');
        $blocks = json_decode($request->input('blocks', "[]"));
        $idx = 1;
        foreach($blocks as $blockId)
        {
            $block = Block::find($blockId);
            if($block)
            {
                $block->order = $idx;
                $block->save();
                $idx++;
            }
        }
        return redirect()->back()
            ->with('success-status', __('locations.block.updated'));
    }

    public function destroy(Block $block)
    {
        Gate::authorize('delete', $block);
        $campus_id = $block->campus_id;
        $block->delete();
        return redirect(route('locations.campuses.show', ['campus' => $campus_id]))
            ->with('success-status', __('locations.period.deleted'));
    }

	public static function middleware()
	{
		return ['auth'];
	}
}
